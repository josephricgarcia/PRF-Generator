<?php
include 'db.php'; // Include database connection

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input data
    $prf = htmlspecialchars($_POST['prf']);
    $pos = htmlspecialchars($_POST['pos']);
    $rep = htmlspecialchars($_POST['rep']);
    $job = htmlspecialchars($_POST['job']);
    $date_req = $_POST['date_req'];
    $date_needed = $_POST['date_needed'];
    $num_needed = intval($_POST['num_needed']);
    
    // Checkbox values (1 for checked, 0 for unchecked)
    $replacement = isset($_POST['reason_type']) && $_POST['reason_type'] === 'replacement' ? 1 : 0;
    $manning = isset($_POST['reason_type']) && $_POST['reason_type'] === 'manning' ? 1 : 0;
    $others_reason = isset($_POST['reason_type']) && $_POST['reason_type'] === 'others_reason' ? 1 : 0;
    $laptop = isset($_POST['laptop']) ? 1 : 0;
    $mobile = isset($_POST['mobile']) ? 1 : 0;
    $phone = isset($_POST['phone']) ? 1 : 0;
    $office = isset($_POST['office']) ? 1 : 0;
    $uniform = isset($_POST['uniform']) ? 1 : 0;
    $table = isset($_POST['table']) ? 1 : 0;
    $chair = isset($_POST['chair']) ? 1 : 0;
    $others_requirement = isset($_POST['others_requirement']) ? 1 : 0;
    
    // Quantities
    $laptop_qty = isset($_POST['laptop_qty']) ? intval($_POST['laptop_qty']) : 0;
    $mobile_qty = isset($_POST['mobile_qty']) ? intval($_POST['mobile_qty']) : 0;
    $phone_qty = isset($_POST['phone_qty']) ? intval($_POST['phone_qty']) : 0;
    $office_qty = isset($_POST['office_qty']) ? intval($_POST['office_qty']) : 0;
    $uniform_qty = isset($_POST['uniform_qty']) ? intval($_POST['uniform_qty']) : 0;
    $table_qty = isset($_POST['table_qty']) ? intval($_POST['table_qty']) : 0;
    $chair_qty = isset($_POST['chair_qty']) ? intval($_POST['chair_qty']) : 0;
    
    // Process array fields
    $rep_of_names = isset($_POST['rep_of_name']) ? json_encode(array_filter($_POST['rep_of_name'], 'strlen')) : json_encode([]);
    $app_names = isset($_POST['app_name']) ? json_encode(array_filter($_POST['app_name'], 'strlen')) : json_encode([]);
    $manning_specs = isset($_POST['manning_spec']) ? json_encode(array_filter($_POST['manning_spec'], 'strlen')) : json_encode([]);
    $others_reason_specs = isset($_POST['others_reason_spec']) ? json_encode(array_filter($_POST['others_reason_spec'], 'strlen')) : json_encode([]);
    $others_requirement_specs = isset($_POST['others_requirement_spec']) ? json_encode(array_filter($_POST['others_requirement_spec'], 'strlen')) : json_encode([]);
    
    // Determine status based on number needed vs provided inputs
    $status = 'pending';
    if ($replacement) {
        $rep_count = count(json_decode($rep_of_names, true));
        $app_count = count(json_decode($app_names, true));
        if ($num_needed <= $rep_count && $num_needed <= $app_count) {
            $status = 'completed';
        } else {
            $remaining = max($num_needed - $rep_count, $num_needed - $app_count);
            $status = "pending (lacking $remaining needed)";
        }
    } elseif ($manning) {
        $manning_count = count(json_decode($manning_specs, true));
        if ($num_needed <= $manning_count) {
            $status = 'completed';
        } else {
            $remaining = $num_needed - $manning_count;
            $status = "pending (lacking $remaining needed)";
        }
    } elseif ($others_reason) {
        $others_count = count(json_decode($others_reason_specs, true));
        if ($num_needed <= $others_count) {
            $status = 'completed';
        } else {
            $remaining = $num_needed - $others_count;
            $status = "pending (lacking $remaining needed)";
        }
    }
    
    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO replacement_forms (
        prf_no, position_title, reports_to, job_level, 
        reason_replacement, rep_of_name, applicant_names,
        reason_manning, manning_spec, reason_others, others_reason_spec,
        date_requested, date_needed, number_needed,
        requirement_laptop, laptop_qty, requirement_mobile, mobile_qty,
        requirement_phone, phone_qty, requirement_office, office_qty,
        requirement_uniform, uniform_qty, requirement_table, table_qty,
        requirement_chair, chair_qty, requirement_others, others_requirement_spec,
        status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param("ssssissssssssiiiiiiiiiiiiiiiiis", 
        $prf, $pos, $rep, $job,
        $replacement, $rep_of_names, $app_names,
        $manning, $manning_specs, $others_reason, $others_reason_specs,
        $date_req, $date_needed, $num_needed,
        $laptop, $laptop_qty, $mobile, $mobile_qty,
        $phone, $phone_qty, $office, $office_qty,
        $uniform, $uniform_qty, $table, $table_qty,
        $chair, $chair_qty, $others_requirement, $others_requirement_specs,
        $status
    );
    
    // Execute and handle result
    if ($stmt->execute()) {
        $success = "PRF form submitted successfully! Status: " . ucfirst($status);
    } else {
        $error = "Error: " . $stmt->error;
    }
    
    $stmt->close();
}
$conn->close();
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CREATE REPLACEMENT FORM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar {
            transition: all 0.3s ease;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                overflow: hidden;
            }
            .sidebar.active {
                width: 250px;
            }
        }
        .input-with-delete {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 6px;
        }
        .error { color: red; font-size: 0.75rem; margin-top: 0.15rem; }
        
        /* Compact form styles */
        .compact-input {
            padding: 0.4rem 0.6rem;
            font-size: 0.875rem;
            height: 2rem;
        }
        .compact-label {
            font-size: 0.875rem;
            margin-bottom: 0.2rem;
        }
        .compact-btn {
            padding: 0.3rem 0.6rem;
            font-size: 0.8125rem;
            margin-bottom: 8px;
        }
        .compact-section {
            gap: 0.8rem;
        }
        .form-group {
            margin-bottom: 0.8rem;
        }
        .checkbox-group {
            margin-bottom: 0.4rem;
        }
        .section-title {
            font-weight: 600;
            margin: 0.8rem 0 0.5rem;
            font-size: 0.95rem;
            color: #333;
            padding-bottom: 0.2rem;
            border-bottom: 1px solid #eee;
        }
        
        /* Comparison styles */
        .comparison-container {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 4px;
        }
        .comparison-bar {
            flex-grow: 1;
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
        }
        .comparison-fill {
            height: 100%;
            background: #3b82f6;
            border-radius: 4px;
            transition: width 0.3s ease;
        }
        .comparison-text {
            font-size: 0.75rem;
            color: #4b5563;
            white-space: nowrap;
        }
        
        /* Status badges */
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .status-completed {
            background-color: #dcfce7;
            color: #166534;
        }
        .status-pending {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        /* Disabled reason styles */
        .disabled-section {
            opacity: 0.6;
        }
        .disabled-section input:not([type="radio"]),
        .disabled-section button:not(.reason-radio) {
            pointer-events: none;
            background-color: #f3f4f6;
            border-color: #e5e7eb;
        }
        .disabled-section button:not(.reason-radio) {
            background-color: #9ca3af !important;
        }
        
        /* Nested section styling */
        .nested-section {
            margin-left: 1.5rem;
            border-left: 2px solid #e5e7eb;
            padding-left: 1rem;
            margin-top: 0.5rem;
        }
        
        /* Radio button styles */
        .reason-radio {
            pointer-events: auto !important;
            opacity: 1 !important;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div class="sidebar bg-orange-600 text-white flex-shrink-0">
            <div class="p-3 flex items-center justify-between border-b border-orange-500">
                <div class="flex items-center space-x-2">
                    <img src="images/be-logo.png" alt="Logo" class="w-8 h-8 rounded-xl object-cover">
                    <span class="text-lg font-bold">PRF System</span>
                </div>
                <button id="sidebarToggle" class="md:hidden">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <nav class="p-3">
                <div class="mb-6">
                    <h3 class="text-orange-300 uppercase text-xs font-semibold mb-3">Main Menu</h3>
                    <ul class="space-y-1">
                        <li>
                            <a href="dashboard.php" class="flex items-center space-x-2 p-2 rounded hover:bg-white hover:text-orange-600">
                                <i class="fas fa-tachometer-alt w-4"></i>
                                <span class="text-sm">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="view-form.php" class="flex items-center space-x-2 p-2 rounded hover:bg-white hover:text-orange-600">
                                <i class="fas fa-folder w-4"></i>
                                <span class="text-sm">View PRF File</span>
                            </a>
                        </li>
                        <li>
                            <a href="view-documents.php" class="flex items-center space-x-2 p-2 rounded hover:bg-white hover:text-orange-600">
                                <i class="fas fa-file w-4"></i>
                                <span class="text-sm">View Scanned Documents</span>
                            </a>
                        </li>
                        <li>
                            <a href="create-replacement-form.php" class="flex items-center space-x-2 p-2 rounded bg-white text-orange-600">
                                <i class="fas fa-file-alt w-4"></i>
                                <span class="text-sm">Create Replacement Form</span>
                            </a>
                        </li>
                        <li>
                            <a href="create-oncall-form.php" class="flex items-center space-x-2 p-2 rounded hover:bg-white hover:text-orange-600">
                                <i class="fas fa-file-alt w-4"></i>
                                <span class="text-sm">Create On Call Form</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow-sm">
                <div class="flex items-center justify-between p-3">
                    <div class="flex items-center">
                        <button id="mobileSidebarToggle" class="md:hidden mr-3">
                            <i class="fas fa-bars text-gray-600"></i>
                        </button>
                        <h1 class="text-lg font-semibold text-gray-800">Create Replacement Form</h1>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-4">
                <div class="max-w-6xl mx-auto">
                    <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
                        <?php if (isset($success)): ?>
                            <script>
                                alert(<?php echo json_encode($success); ?>);
                            </script>
                        <?php elseif (isset($error)): ?>
                            <script>
                                alert(<?php echo json_encode($error); ?>);
                            </script>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                        <form action="" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4 compact-section">
                            <!-- Left Column -->
                            <div class="space-y-3">
                                <div class="form-group">
                                    <label for="prf" class="block compact-label">PRF No:</label>
                                    <input type="text" id="prf" name="prf" class="w-full compact-input border rounded focus:outline-none focus:border-orange-600" required>
                                </div>

                                <div class="form-group">
                                    <label for="pos" class="block compact-label">Position Title:</label>
                                    <input type="text" id="pos" name="pos" class="w-full compact-input border rounded focus:outline-none focus:border-orange-600" required>
                                </div>

                                <div class="form-group">
                                    <label for="rep" class="block compact-label">Reports to:</label>
                                    <input type="text" id="rep" name="rep" class="w-full compact-input border rounded focus:outline-none focus:border-orange-600" required>
                                </div>

                                <div class="form-group">
                                    <label for="job" class="block compact-label">Job Level:</label>
                                    <input type="text" id="job" name="job" class="w-full compact-input border rounded focus:outline-none focus:border-orange-600" required>
                                </div>

                                <div class="section-title">Reason for Request:</div>
                                
                                <!-- Replacement of Section with nested Applicant Names -->
                                <div class="checkbox-group border-l-2 border-gray-300 pl-4 mb-4 disabled-section">
                                    <div class="flex items-center text-sm mb-2">
                                        <input type="radio" name="reason_type" id="replacement" value="replacement" class="mr-2 reason-radio">
                                        <label for="replacement" class="font-medium">Replacement of:</label>
                                    </div>
                                    
                                    <div id="replacement-fields" class="ml-6">
                                        <div class="input-with-delete">
                                            <input type="text" name="rep_of_name[]" class="w-full compact-input border rounded focus:outline-none focus:border-orange-600" placeholder="Specify name" disabled>
                                            <button type="button" class="delete-field-btn bg-red-500 text-white px-1.5 py-0.5 rounded text-xs hover:bg-red-600" disabled>
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <button type="button" id="addReplacementField" class="bg-blue-500 text-white compact-btn rounded text-sm hover:bg-blue-600 ml-6" disabled>Add name</button>
                                    
                                    <!-- Applicant Names nested under Replacement -->
                                    <div class="nested-section disabled-section">
                                        <label class="block compact-label">Applicant Name(s):</label>
                                        <div id="applicant-fields">
                                            <div class="input-with-delete">
                                                <input type="text" name="app_name[]" class="w-full compact-input border rounded focus:outline-none focus:border-orange-600" placeholder="Specify applicant" disabled>
                                                <button type="button" class="delete-field-btn bg-red-500 text-white px-1.5 py-0.5 rounded text-xs hover:bg-red-600" disabled>
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <button type="button" id="addApplicantField" class="bg-blue-500 text-white compact-btn rounded text-sm hover:bg-blue-600" disabled>Add applicant</button>
                                    </div>
                                </div>

                                <!-- Additional Manning Section -->
                                <div class="checkbox-group border-l-2 border-gray-300 pl-4 mb-4 disabled-section">
                                    <div class="flex items-center text-sm mb-2">
                                        <input type="radio" name="reason_type" id="manning" value="manning" class="mr-2 reason-radio">
                                        <label for="manning" class="font-medium">Additional Manning:</label>
                                    </div>
                                    
                                    <div id="manning-fields" class="ml-6">
                                        <div class="input-with-delete">
                                            <input type="text" name="manning_spec[]" class="w-full compact-input border rounded focus:outline-none focus:border-orange-600" placeholder="Specify manning" disabled required>
                                            <button type="button" class="delete-field-btn bg-red-500 text-white px-1.5 py-0.5 rounded text-xs hover:bg-red-600" disabled>
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <button type="button" id="addManningField" class="bg-blue-500 text-white compact-btn rounded text-sm hover:bg-blue-600 ml-6" disabled>Add manning</button>
                                </div>

                                <!-- Others Section -->
                                <div class="checkbox-group border-l-2 border-gray-300 pl-4 disabled-section">
                                    <div class="flex items-center text-sm mb-2">
                                        <input type="radio" name="reason_type" id="others_reason" value="others_reason" class="mr-2 reason-radio">
                                        <label for="others_reason" class="font-medium">Others:</label>
                                    </div>
                                    
                                    <div id="others-reason-fields" class="ml-6">
                                        <div class="input-with-delete">
                                            <input type="text" name="others_reason_spec[]" class="w-full compact-input border rounded focus:outline-none focus:border-orange-600" placeholder="Specify reason" disabled required>
                                            <button type="button" class="delete-field-btn bg-red-500 text-white px-1.5 py-0.5 rounded text-xs hover:bg-red-600" disabled>
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <button type="button" id="addOthersReasonField" class="bg-blue-500 text-white compact-btn rounded text-sm hover:bg-blue-600 ml-6" disabled>Add reason</button>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="space-y-3">
                                <div class="form-group">
                                    <label for="date_req" class="block compact-label">Date Requested:</label>
                                    <input type="date" id="date_req" name="date_req" class="w-full compact-input border rounded focus:outline-none focus:border-orange-600" required>
                                </div>

                                <div class="form-group">
                                    <label for="date_needed" class="block compact-label">Date Needed:</label>
                                    <input type="date" id="date_needed" name="date_needed" class="w-full compact-input border rounded focus:outline-none focus:border-orange-600" required>
                                </div>

                                <div class="form-group">
                                    <label for="num_needed" class="block compact-label">Number Needed:</label>
                                    <input type="number" id="num_needed" name="num_needed" min="1" class="w-full compact-input border rounded focus:outline-none focus:border-orange-600" required>
                                    <div id="comparison-display" class="mt-1"></div>
                                    <div id="status-preview" class="mt-1 text-sm"></div>
                                </div>

                                <div class="section-title">POSITION REQUIREMENTS:</div>
                                
                                <div class="grid grid-cols-[auto_1fr] gap-2 text-sm">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="laptop" id="laptop" class="mr-2">
                                        <label for="laptop">Laptop/Desktop:</label>
                                    </div>
                                    <input type="number" name="laptop_qty" class="w-full compact-input border rounded focus:outline-none focus:border-orange-600" required disabled>
                                    <div class="flex items-center">
                                        <input type="checkbox" name="mobile" id="mobile" class="mr-2">
                                        <label for="mobile">Mobile Unit:</label>
                                    </div>
                                    <input type="number" name="mobile_qty" class="w-full compact-input border rounded focus:outline-none focus:border-orange-600" required disabled>
                                    
                                    <div class="flex items-center">
                                        <input type="checkbox" name="phone" id="phone" class="mr-2">
                                        <label for="phone">Phone Plan:</label>
                                    </div>
                                    <input type="number" name="phone_qty" class="w-full compact-input border rounded focus:outline-none focus:border-orange-600" required disabled>
                                    
                                    <div class="flex items-center">
                                        <input type="checkbox" name="office" id="office" class="mr-2">
                                        <label for="office">Office/Desk Space:</label>
                                    </div>
                                    <input type="number" name="office_qty" class="w-full compact-input border rounded focus:outline-none focus:border-orange-600" required disabled>
                                    
                                    <div class="flex items-center">
                                        <input type="checkbox" name="uniform" id="uniform" class="mr-2">
                                        <label for="uniform">Uniform:</label>
                                    </div>
                                    <input type="number" name="uniform_qty" class="w-full compact-input border rounded focus:outline-none focus:border-orange-600" required disabled>
                                    
                                    <div class="flex items-center">
                                        <input type="checkbox" name="table" id="table" class="mr-2">
                                        <label for="table">Table:</label>
                                    </div>
                                    <input type="number" name="table_qty" class="w-full compact-input border rounded focus:outline-none focus:border-orange-600" required disabled>
                                    
                                    <div class="flex items-center">
                                        <input type="checkbox" name="chair" id="chair" class="mr-2">
                                        <label for="chair">Chair:</label>
                                    </div>
                                    <input type="number" name="chair_qty" class="w-full compact-input border rounded focus:outline-none focus:border-orange-600" required disabled>
                                    
                                    <div class="flex items-center">
                                        <input type="checkbox" name="others_requirement" id="others_requirement" class="mr-2">
                                        <label for="others_requirement">Others:</label>
                                    </div>
                                    <div class="col-span-2">
                                        <div id="others-requirement-fields">
                                            <div class="input-with-delete">
                                                <input type="text" name="others_requirement_spec[]" class="w-full compact-input border rounded focus:outline-none focus:border-orange-600" required disabled placeholder="Specify requirement">
                                                <button type="button" class="delete-field-btn bg-red-500 text-white px-1.5 py-0.5 rounded text-xs hover:bg-red-600" disabled>
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <button type="button" id="addOthersRequirementField" class="bg-blue-500 text-white compact-btn rounded text-sm hover:bg-blue-600" disabled>Add requirement</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Form buttons -->
                            <div class="col-span-1 md:col-span-2 mt-4 flex space-x-3">
                                <button type="submit" class="bg-orange-600 text-white py-1.5 px-4 rounded text-sm hover:bg-orange-700 flex items-center">
                                    <i class="fas fa-save mr-1 text-xs"></i> Save
                                </button>
                                <button type="reset" class="bg-gray-300 text-gray-700 py-1.5 px-4 rounded text-sm hover:bg-gray-400 flex items-center">
                                    <i class="fas fa-undo mr-1 text-xs"></i> Reset
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Toggle sidebar for mobile view
        document.getElementById('mobileSidebarToggle').addEventListener('click', () => {
            document.querySelector('.sidebar').classList.toggle('active');
        });

        // Function to create a new input field with a delete button
        function createInputFieldWithDelete(name, placeholder, disabled = false) {
            const wrapperDiv = document.createElement('div');
            wrapperDiv.className = 'input-with-delete';

            const newInput = document.createElement('input');
            newInput.type = 'text';
            newInput.name = name;
            newInput.className = 'w-full compact-input border rounded';
            newInput.placeholder = placeholder;
            if (disabled) {
                newInput.disabled = true;
                newInput.classList.add('disabled-section');
            }
            newInput.addEventListener('input', updateReasonComparison);

            const deleteButton = document.createElement('button');
            deleteButton.type = 'button';
            deleteButton.className = `delete-field-btn bg-red-500 text-white px-1.5 py-0.5 rounded text-xs hover:bg-red-600 ${disabled ? 'disabled-section' : ''}`;
            deleteButton.innerHTML = '<i class="fas fa-trash-alt"></i>';
            if (disabled) {
                deleteButton.disabled = true;
            }
            deleteButton.addEventListener('click', () => {
                wrapperDiv.remove();
                updateReasonComparison();
            });

            wrapperDiv.appendChild(newInput);
            wrapperDiv.appendChild(deleteButton);

            return wrapperDiv;
        }

        // Add Replacement Field
        document.getElementById('addReplacementField').addEventListener('click', () => {
            const replacementFieldsDiv = document.getElementById('replacement-fields');
            const isDisabled = replacementFieldsDiv.parentElement.classList.contains('disabled-section');
            replacementFieldsDiv.appendChild(createInputFieldWithDelete('rep_of_name[]', 'Specify name', isDisabled));
        });

        // Add Applicant Field
        document.getElementById('addApplicantField').addEventListener('click', () => {
            const applicantFieldsDiv = document.getElementById('applicant-fields');
            const isDisabled = applicantFieldsDiv.closest('.checkbox-group').classList.contains('disabled-section');
            applicantFieldsDiv.appendChild(createInputFieldWithDelete('app_name[]', 'Specify applicant', isDisabled));
        });

        // Add Manning Field
        document.getElementById('addManningField').addEventListener('click', () => {
            const manningFieldsDiv = document.getElementById('manning-fields');
            const isDisabled = manningFieldsDiv.parentElement.classList.contains('disabled-section');
            manningFieldsDiv.appendChild(createInputFieldWithDelete('manning_spec[]', 'Specify manning', isDisabled));
        });

        // Add Others Reason Field
        document.getElementById('addOthersReasonField').addEventListener('click', () => {
            const othersReasonFieldsDiv = document.getElementById('others-reason-fields');
            const isDisabled = othersReasonFieldsDiv.parentElement.classList.contains('disabled-section');
            othersReasonFieldsDiv.appendChild(createInputFieldWithDelete('others_reason_spec[]', 'Specify reason', isDisabled));
        });

        // Add Others Requirement Field
        document.getElementById('addOthersRequirementField').addEventListener('click', () => {
            const othersRequirementFieldsDiv = document.getElementById('others-requirement-fields');
            othersRequirementFieldsDiv.appendChild(createInputFieldWithDelete('others_requirement_spec[]', 'Specify requirement'));
        });

        // Initialize delete buttons for pre-existing fields
        document.querySelectorAll('.delete-field-btn').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.input-with-delete').remove();
                updateReasonComparison();
            });
        });

        // Handle reason type selection
        document.querySelectorAll('input[name="reason_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const selectedReason = this.value;
                
                // Get all reason sections
                const replacementSection = document.querySelector('#replacement').closest('.checkbox-group');
                const applicantSection = document.querySelector('#applicant-fields').closest('.nested-section');
                const manningSection = document.querySelector('#manning').closest('.checkbox-group');
                const othersReasonSection = document.querySelector('#others_reason').closest('.checkbox-group');
                
                // Reset all sections' border colors and disabled states
                [replacementSection, applicantSection, manningSection, othersReasonSection].forEach(section => {
                    const inputs = section.querySelectorAll('input:not([type="radio"])');
                    const buttons = section.querySelectorAll('button:not(.reason-radio)');
                    
                    // Set border color to gray
                    section.classList.remove('border-orange-500');
                    section.classList.add('border-gray-300');
                    
                    // Disable inputs and buttons
                    inputs.forEach(input => {
                        input.disabled = true;
                        input.closest('.input-with-delete')?.classList.add('disabled-section');
                    });
                    
                    buttons.forEach(button => {
                        button.disabled = true;
                        button.classList.add('disabled-section');
                    });
                    
                    section.classList.add('disabled-section');
                });
                
                // Enable selected section and set orange border
                if (selectedReason === 'replacement') {
                    replacementSection.classList.remove('disabled-section', 'border-gray-300');
                    replacementSection.classList.add('border-orange-500');
                    applicantSection.classList.remove('disabled-section', 'border-gray-300');
                    applicantSection.classList.add('border-orange-500');
                    
                    const replacementInputs = replacementSection.querySelectorAll('input:not([type="radio"])');
                    const replacementButtons = replacementSection.querySelectorAll('button:not(.reason-radio)');
                    const applicantInputs = applicantSection.querySelectorAll('input');
                    const applicantButtons = applicantSection.querySelectorAll('button');
                    
                    replacementInputs.forEach(input => {
                        input.disabled = false;
                        input.closest('.input-with-delete')?.classList.remove('disabled-section');
                    });
                    
                    replacementButtons.forEach(button => {
                        button.disabled = false;
                        button.classList.remove('disabled-section');
                    });
                    
                    applicantInputs.forEach(input => {
                        input.disabled = false;
                        input.closest('.input-with-delete')?.classList.remove('disabled-section');
                    });
                    
                    applicantButtons.forEach(button => {
                        button.disabled = false;
                        button.classList.remove('disabled-section');
                    });
                } 
                else if (selectedReason === 'manning') {
                    manningSection.classList.remove('disabled-section', 'border-gray-300');
                    manningSection.classList.add('border-orange-500');
                    
                    const manningInputs = manningSection.querySelectorAll('input:not([type="radio"])');
                    const manningButtons = manningSection.querySelectorAll('button:not(.reason-radio)');
                    
                    manningInputs.forEach(input => {
                        input.disabled = false;
                        input.closest('.input-with-delete')?.classList.remove('disabled-section');
                    });
                    
                    manningButtons.forEach(button => {
                        button.disabled = false;
                        button.classList.remove('disabled-section');
                    });
                } 
                else if (selectedReason === 'others_reason') {
                    othersReasonSection.classList.remove('disabled-section', 'border-gray-300');
                    othersReasonSection.classList.add('border-orange-500');
                    
                    const othersInputs = othersReasonSection.querySelectorAll('input:not([type="radio"])');
                    const othersButtons = othersReasonSection.querySelectorAll('button:not(.reason-radio)');
                    
                    othersInputs.forEach(input => {
                        input.disabled = false;
                        input.closest('.input-with-delete')?.classList.remove('disabled-section');
                    });
                    
                    othersButtons.forEach(button => {
                        button.disabled = false;
                        button.classList.remove('disabled-section');
                    });
                }

                // Update comparison
                updateReasonComparison();
            });
        });

        // Function to calculate and display comparison
        function updateReasonComparison() {
            const numNeeded = parseInt(document.getElementById('num_needed').value) || 0;
            const selectedReason = document.querySelector('input[name="reason_type"]:checked')?.value;
            
            if (numNeeded <= 0 || !selectedReason) {
                document.getElementById('comparison-display').innerHTML = '';
                document.getElementById('status-preview').innerHTML = '';
                return;
            }

            let reasonCount = 0;
            let comparisonText = '';
            let percentage = 0;
            
            if (selectedReason === 'replacement') {
                // Count replacement names
                const repNames = Array.from(document.querySelectorAll('input[name="rep_of_name[]"]'))
                    .filter(input => input.value.trim() !== '').length;
                
                // Count applicant names
                const appNames = Array.from(document.querySelectorAll('input[name="app_name[]"]'))
                    .filter(input => input.value.trim() !== '').length;
                
                // Update status preview
                let statusText;
                if (numNeeded <= repNames && numNeeded <= appNames) {
                    statusText = 'Will be marked as: <span class="status-badge status-completed">Completed</span>';
                } else {
                    const remaining = Math.max(numNeeded - repNames, numNeeded - appNames);
                    statusText = `Will be marked as: <span class="status-badge status-pending">Pending (lacking ${remaining} needed)</span>`;
                }
                
                // Show detailed comparison
                comparisonText = `Names: ${repNames}, Applicants: ${appNames}, Needed: ${numNeeded}`;
                percentage = Math.min(100, Math.round((Math.min(repNames, appNames) / numNeeded) * 100));
                document.getElementById('status-preview').innerHTML = statusText;
            } 
            else if (selectedReason === 'manning') {
                reasonCount = Array.from(document.querySelectorAll('input[name="manning_spec[]"]'))
                    .filter(input => input.value.trim() !== '').length;
                
                // Update status preview
                let statusText;
                if (numNeeded <= reasonCount) {
                    statusText = 'Will be marked as: <span class="status-badge status-completed">Completed</span>';
                } else {
                    const remaining = numNeeded - reasonCount;
                    statusText = `Will be marked as: <span class="status-badge status-pending">Pending (lacking ${remaining} needed)</span>`;
                }
                
                comparisonText = `Manning Specs: ${reasonCount}, Needed: ${numNeeded}`;
                percentage = Math.min(100, Math.round((reasonCount / numNeeded) * 100));
                document.getElementById('status-preview').innerHTML = statusText;
            } 
            else if (selectedReason === 'others_reason') {
                reasonCount = Array.from(document.querySelectorAll('input[name="others_reason_spec[]"]'))
                    .filter(input => input.value.trim() !== '').length;
                
                // Update status preview
                let statusText;
                if (numNeeded <= reasonCount) {
                    statusText = 'Will be marked as: <span class="status-badge status-completed">Completed</span>';
                } else {
                    const remaining = numNeeded - reasonCount;
                    statusText = `Will be marked as: <span class="status-badge status-pending">Pending (lacking ${remaining} needed)</span>`;
                }
                
                comparisonText = `Reasons: ${reasonCount}, Needed: ${numNeeded}`;
                percentage = Math.min(100, Math.round((reasonCount / numNeeded) * 100));
                document.getElementById('status-preview').innerHTML = statusText;
            }

            // Create comparison display
            const displayHTML = `
                <div class="comparison-container">
                    <div class="comparison-bar">
                        <div class="comparison-fill" style="width: ${percentage}%"></div>
                    </div>
                    <div class="comparison-text">
                        ${comparisonText}
                    </div>
                </div>
            `;
            
            document.getElementById('comparison-display').innerHTML = displayHTML;
        }

        // Toggle quantity fields based on checkbox state
        function toggleQuantityInput(checkboxId, inputName) {
            const checkbox = document.getElementById(checkboxId);
            const input = document.querySelector(`input[name="${inputName}"]`);
            
            checkbox.addEventListener('change', function() {
                input.disabled = !this.checked;
                if (this.checked) {
                    input.focus();
                }
            });
        }

        // Set up all requirement toggles
        toggleQuantityInput('laptop', 'laptop_qty');
        toggleQuantityInput('mobile', 'mobile_qty');
        toggleQuantityInput('phone', 'phone_qty');
        toggleQuantityInput('office', 'office_qty');
        toggleQuantityInput('uniform', 'uniform_qty');
        toggleQuantityInput('table', 'table_qty');
        toggleQuantityInput('chair', 'chair_qty');

        // Handle others requirement toggle
        document.getElementById('others_requirement').addEventListener('change', function() {
            const isChecked = this.checked;
            const othersInputs = document.querySelectorAll('input[name="others_requirement_spec[]"]');
            const othersButtons = document.querySelectorAll('#others-requirement-fields .delete-field-btn, #addOthersRequirementField');
            
            othersInputs.forEach(input => {
                input.disabled = !isChecked;
            });
            
            othersButtons.forEach(button => {
                button.disabled = !isChecked;
            });
            
            if (isChecked && othersInputs.length === 0) {
                document.getElementById('addOthersRequirementField').click();
            }
        });

        // Add event listeners to trigger comparison updates
        document.getElementById('num_needed').addEventListener('input', updateReasonComparison);
        
        // Update when dynamic fields change
        const containers = [
            'replacement-fields', 
            'applicant-fields', 
            'manning-fields', 
            'others-reason-fields'
        ];
        
        containers.forEach(containerId => {
            document.getElementById(containerId).addEventListener('input', updateReasonComparison);
        });

        // Initialize form with no reason selected
        document.addEventListener('DOMContentLoaded', () => {
            const replacementSection = document.querySelector('#replacement').closest('.checkbox-group');
            const applicantSection = document.querySelector('#applicant-fields').closest('.nested-section');
            const manningSection = document.querySelector('#manning').closest('.checkbox-group');
            const othersReasonSection = document.querySelector('#others_reason').closest('.checkbox-group');

            // Ensure all sections are disabled by default
            [replacementSection, applicantSection, manningSection, othersReasonSection].forEach(section => {
                section.classList.add('disabled-section', 'border-gray-300');
                section.classList.remove('border-orange-500');
                
                const inputs = section.querySelectorAll('input:not([type="radio"])');
                const buttons = section.querySelectorAll('button:not(.reason-radio)');

                inputs.forEach(input => {
                    input.disabled = true;
                    input.closest('.input-with-delete')?.classList.add('disabled-section');
                });

                buttons.forEach(button => {
                    button.disabled = true;
                    button.classList.add('disabled-section');
                });
            });

            // Clear comparison display initially
            updateReasonComparison();
        });
    </script>
</body>
</html>