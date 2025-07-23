<?php
include 'db.php'; // Include database connection

// Initialize variables
$prf_no = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : '';
$form_type = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : '';
$form_data = null;
$error = '';

// Validate form type to prevent SQL injection
$valid_form_types = ['replacement', 'oncall'];
if (!in_array($form_type, $valid_form_types)) {
    $error = "Invalid form type.";
} elseif (empty($prf_no)) {
    $error = "PRF number is required.";
} else {
    // Determine table name based on form type
    $table = ($form_type === 'replacement') ? 'replacement_forms' : 'oncall_forms';
    
    // Check if database connection is successful
    if (!$conn) {
        $error = "Database connection failed: " . mysqli_connect_error();
    } else {
        // Prepare and execute query
        $stmt = $conn->prepare("SELECT * FROM `$table` WHERE prf_no = ?");
        if (!$stmt) {
            $error = "Query preparation failed: " . $conn->error;
        } else {
            $stmt->bind_param("s", $prf_no);
            if (!$stmt->execute()) {
                $error = "Query execution failed: " . $stmt->error;
            } else {
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $form_data = $result->fetch_assoc();
                } else {
                    $error = "Form not found for PRF No: " . htmlspecialchars($prf_no);
                }
            }
            $stmt->close();
        }
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview PRF Form</title>
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
            pointer-events: none !important;
            opacity: 1 !important;
        }
        
        /* Read-only styles */
        .readonly-input {
            background-color: #f3f4f6;
            pointer-events: none;
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
                            <a href="create-replacement-form.php" class="flex items-center space-x-2 p-2 rounded hover:bg-white hover:text-orange-600">
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
                        <h1 class="text-lg font-semibold text-gray-800">Preview <?php echo htmlspecialchars(ucfirst($form_type)); ?> Form</h1>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-4">
                <div class="max-w-6xl mx-auto">
                    <?php if ($error): ?>
                        <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-4">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php elseif ($form_data): ?>
                        <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                            <form class="grid grid-cols-1 md:grid-cols-2 gap-4 compact-section">
                                <!-- Left Column -->
                                <div class="space-y-3">
                                    <?php if (!empty($form_data['prf_no'])): ?>
                                    <div class="form-group">
                                        <label for="prf" class="block compact-label">PRF No:</label>
                                        <input type="text" id="prf" name="prf" class="w-full compact-input border rounded readonly-input" value="<?php echo htmlspecialchars($form_data['prf_no']); ?>" readonly>
                                    </div>
                                    <?php endif; ?>

                                    <?php if (!empty($form_data['position_title'])): ?>
                                    <div class="form-group">
                                        <label for="pos" class="block compact-label">Position Title:</label>
                                        <input type="text" id="pos" name="pos" class="w-full compact-input border rounded readonly-input" value="<?php echo htmlspecialchars($form_data['position_title']); ?>" readonly>
                                    </div>
                                    <?php endif; ?>

                                    <?php if (!empty($form_data['reports_to'])): ?>
                                    <div class="form-group">
                                        <label for="rep" class="block compact-label">Reports to:</label>
                                        <input type="text" id="rep" name="rep" class="w-full compact-input border rounded readonly-input" value="<?php echo htmlspecialchars($form_data['reports_to']); ?>" readonly>
                                    </div>
                                    <?php endif; ?>

                                    <?php if (!empty($form_data['job_level'])): ?>
                                    <div class="form-group">
                                        <label for="job" class="block compact-label">Job Level:</label>
                                        <input type="text" id="job" name="job" class="w-full compact-input border rounded readonly-input" value="<?php echo htmlspecialchars($form_data['job_level']); ?>" readonly>
                                    </div>
                                    <?php endif; ?>

                                    <?php if ($form_data['reason_replacement'] || $form_data['reason_manning'] || $form_data['reason_others']): ?>
                                    <div class="section-title">Reason for Request:</div>
                                    <?php endif; ?>
                                    
                                    <!-- Replacement of Section with nested Applicant Names -->
                                    <?php if ($form_data['reason_replacement']): ?>
                                    <div class="checkbox-group border-l-2 border-orange-500 pl-4 mb-4">
                                        <div class="flex items-center text-sm mb-2">
                                            <input type="radio" name="reason_type" id="replacement" value="replacement" class="mr-2 reason-radio" checked>
                                            <label for="replacement" class="font-medium">Replacement of:</label>
                                        </div>
                                        
                                        <?php
                                        $rep_names = json_decode($form_data['rep_of_name'] ?? '[]', true);
                                        $rep_names = is_array($rep_names) ? $rep_names : [];
                                        $non_empty_rep_names = array_filter($rep_names, fn($name) => !empty(trim((string)$name)));
                                        if (!empty($non_empty_rep_names)):
                                        ?>
                                        <div id="replacement-fields" class="ml-6">
                                            <?php foreach ($non_empty_rep_names as $name): ?>
                                            <div class="input-with-delete">
                                                <input type="text" name="rep_of_name[]" class="w-full compact-input border rounded readonly-input" value="<?php echo htmlspecialchars($name); ?>" readonly>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <!-- Applicant Names nested under Replacement -->
                                        <?php
                                        $app_names = json_decode($form_data['applicant_names'] ?? '[]', true);
                                        $app_names = is_array($app_names) ? $app_names : [];
                                        $non_empty_app_names = array_filter($app_names, fn($name) => !empty(trim((string)$name)));
                                        if (!empty($non_empty_app_names)):
                                        ?>
                                        <div class="nested-section">
                                            <label class="block compact-label">Applicant Name(s):</label>
                                            <div id="applicant-fields">
                                                <?php foreach ($non_empty_app_names as $name): ?>
                                                <div class="input-with-delete">
                                                    <input type="text" name="app_name[]" class="w-full compact-input border rounded readonly-input" value="<?php echo htmlspecialchars($name); ?>" readonly>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>

                                    <!-- Additional Manning Section -->
                                    <?php if ($form_data['reason_manning']): ?>
                                    <div class="checkbox-group border-l-2 border-orange-500 pl-4 mb-4">
                                        <div class="flex items-center text-sm mb-2">
                                            <input type="radio" name="reason_type" id="manning" value="manning" class="mr-2 reason-radio" checked>
                                            <label for="manning" class="font-medium">Additional Manning:</label>
                                        </div>
                                        
                                        <?php
                                        $manning_specs = json_decode($form_data['manning_spec'] ?? '[]', true);
                                        $manning_specs = is_array($manning_specs) ? $manning_specs : [];
                                        $non_empty_manning_specs = array_filter($manning_specs, fn($spec) => !empty(trim((string)$spec)));
                                        if (!empty($non_empty_manning_specs)):
                                        ?>
                                        <div id="manning-fields" class="ml-6">
                                            <?php foreach ($non_empty_manning_specs as $spec): ?>
                                            <div class="input-with-delete">
                                                <input type="text" name="manning_spec[]" class="w-full compact-input border rounded readonly-input" value="<?php echo htmlspecialchars($spec); ?>" readonly>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>

                                    <!-- Others Section -->
                                    <?php if ($form_data['reason_others']): ?>
                                    <div class="checkbox-group border-l-2 border-orange-500 pl-4">
                                        <div class="flex items-center text-sm mb-2">
                                            <input type="radio" name="reason_type" id="others_reason" value="others_reason" class="mr-2 reason-radio" checked>
                                            <label for="others_reason" class="font-medium">Others:</label>
                                        </div>
                                        
                                        <?php
                                        $others_reason_specs = json_decode($form_data['others_reason_spec'] ?? '[]', true);
                                        $others_reason_specs = is_array($others_reason_specs) ? $others_reason_specs : [];
                                        $non_empty_others_reason_specs = array_filter($others_reason_specs, fn($spec) => !empty(trim((string)$spec)));
                                        if (!empty($non_empty_others_reason_specs)):
                                        ?>
                                        <div id="others-reason-fields" class="ml-6">
                                            <?php foreach ($non_empty_others_reason_specs as $spec): ?>
                                            <div class="input-with-delete">
                                                <input type="text" name="others_reason_spec[]" class="w-full compact-input border rounded readonly-input" value="<?php echo htmlspecialchars($spec); ?>" readonly>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Right Column -->
                                <div class="space-y-3">
                                    <?php if (!empty($form_data['date_requested'])): ?>
                                    <div class="form-group">
                                        <label for="date_req" class="block compact-label">Date Requested:</label>
                                        <input type="date" id="date_req" name="date_req" class="w-full compact-input border rounded readonly-input" value="<?php echo htmlspecialchars($form_data['date_requested']); ?>" readonly>
                                    </div>
                                    <?php endif; ?>

                                    <?php if (!empty($form_data['date_needed'])): ?>
                                    <div class="form-group">
                                        <label for="date_needed" class="block compact-label">Date Needed:</label>
                                        <input type="date" id="date_needed" name="date_needed" class="w-full compact-input border rounded readonly-input" value="<?php echo htmlspecialchars($form_data['date_needed']); ?>" readonly>
                                    </div>
                                    <?php endif; ?>

                                    <?php if (!empty($form_data['number_needed'])): ?>
                                    <div class="form-group">
                                        <label for="num_needed" class="block compact-label">Number Needed:</label>
                                        <input type="number" id="num_needed" name="num_needed" class="w-full compact-input border rounded readonly-input" value="<?php echo htmlspecialchars($form_data['number_needed']); ?>" readonly>
                                        <div id="comparison-display" class="mt-1"></div>
                                        <div id="status-preview" class="mt-1 text-sm">
                                            <span class="status-badge <?php echo strpos($form_data['status'], 'completed') !== false ? 'status-completed' : 'status-pending'; ?>">
                                                <?php echo htmlspecialchars(ucfirst($form_data['status'])); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <?php if ($form_data['requirement_laptop'] || $form_data['requirement_mobile'] || $form_data['requirement_phone'] || $form_data['requirement_office'] || $form_data['requirement_uniform'] || $form_data['requirement_table'] || $form_data['requirement_chair'] || $form_data['requirement_others']): ?>
                                    <div class="section-title">POSITION REQUIREMENTS:</div>
                                    <div class="grid grid-cols-[auto_1fr] gap-2 text-sm">
                                        <?php if ($form_data['requirement_laptop'] && !empty($form_data['laptop_qty'])): ?>
                                        <div class="flex items-center">
                                            <input type="checkbox" name="laptop" id="laptop" class="mr-2" checked disabled>
                                            <label for="laptop">Laptop/Desktop:</label>
                                        </div>
                                        <input type="number" name="laptop_qty" class="compact-input border rounded readonly-input" value="<?php echo htmlspecialchars($form_data['laptop_qty']); ?>" readonly>
                                        <?php endif; ?>

                                        <?php if ($form_data['requirement_mobile'] && !empty($form_data['mobile_qty'])): ?>
                                        <div class="flex items-center">
                                            <input type="checkbox" name="mobile" id="mobile" class="mr-2" checked disabled>
                                            <label for="mobile">Mobile Unit:</label>
                                        </div>
                                        <input type="number" name="mobile_qty" class="compact-input border rounded readonly-input" value="<?php echo htmlspecialchars($form_data['mobile_qty']); ?>" readonly>
                                        <?php endif; ?>

                                        <?php if ($form_data['requirement_phone'] && !empty($form_data['phone_qty'])): ?>
                                        <div class="flex items-center">
                                            <input type="checkbox" name="phone" id="phone" class="mr-2" checked disabled>
                                            <label for="phone">Phone Plan:</label>
                                        </div>
                                        <input type="number" name="phone_qty" class="compact-input border rounded readonly-input" value="<?php echo htmlspecialchars($form_data['phone_qty']); ?>" readonly>
                                        <?php endif; ?>

                                        <?php if ($form_data['requirement_office'] && !empty($form_data['office_qty'])): ?>
                                        <div class="flex items-center">
                                            <input type="checkbox" name="office" id="office" class="mr-2" checked disabled>
                                            <label for="office">Office/Desk Space:</label>
                                        </div>
                                        <input type="number" name="office_qty" class="compact-input border rounded readonly-input" value="<?php echo htmlspecialchars($form_data['office_qty']); ?>" readonly>
                                        <?php endif; ?>

                                        <?php if ($form_data['requirement_uniform'] && !empty($form_data['uniform_qty'])): ?>
                                        <div class="flex items-center">
                                            <input type="checkbox" name="uniform" id="uniform" class="mr-2" checked disabled>
                                            <label for="uniform">Uniform:</label>
                                        </div>
                                        <input type="number" name="uniform_qty" class="compact-input border rounded readonly-input" value="<?php echo htmlspecialchars($form_data['uniform_qty']); ?>" readonly>
                                        <?php endif; ?>

                                        <?php if ($form_data['requirement_table'] && !empty($form_data['table_qty'])): ?>
                                        <div class="flex items-center">
                                            <input type="checkbox" name="table" id="table" class="mr-2" checked disabled>
                                            <label for="table">Table:</label>
                                        </div>
                                        <input type="number" name="table_qty" class="compact-input border rounded readonly-input" value="<?php echo htmlspecialchars($form_data['table_qty']); ?>" readonly>
                                        <?php endif; ?>

                                        <?php if ($form_data['requirement_chair'] && !empty($form_data['chair_qty'])): ?>
                                        <div class="flex items-center">
                                            <input type="checkbox" name="chair" id="chair" class="mr-2" checked disabled>
                                            <label for="chair">Chair:</label>
                                        </div>
                                        <input type="number" name="chair_qty" class="compact-input border rounded readonly-input" value="<?php echo htmlspecialchars($form_data['chair_qty']); ?>" readonly>
                                        <?php endif; ?>

                                        <?php
                                        $others_requirement_specs = json_decode($form_data['others_requirement_spec'] ?? '[]', true);
                                        $others_requirement_specs = is_array($others_requirement_specs) ? $others_requirement_specs : [];
                                        $non_empty_others_requirement_specs = array_filter($others_requirement_specs, fn($spec) => !empty(trim((string)$spec)));
                                        if ($form_data['requirement_others'] && !empty($non_empty_others_requirement_specs)):
                                        ?>
                                        <div class="flex items-center">
                                            <input type="checkbox" name="others_requirement" id="others_requirement" class="mr-2" checked disabled>
                                            <label for="others_requirement">Others:</label>
                                        </div>
                                        <div class="col-span-2">
                                            <div id="others-requirement-fields">
                                                <?php foreach ($non_empty_others_requirement_specs as $spec): ?>
                                                <div class="input-with-delete">
                                                    <input type="text" name="others_requirement_spec[]" class="w-full compact-input border rounded readonly-input" value="<?php echo htmlspecialchars($spec); ?>" readonly>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Form buttons -->
                                <div class="col-span-1 md:col-span-2 mt-4 flex space-x-3">
                                    <a href="view-form.php" class="bg-orange-600 text-white py-1.5 px-4 rounded text-sm hover:bg-orange-500 flex items-center">
                                        <i class="fas fa-arrow-left mr-1 text-xs"></i> Back to List
                                    </a>
                                </div>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="bg-yellow-100 text-yellow-700 p-4 rounded-lg mb-4">
                            Please select a valid PRF form to preview.
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Toggle sidebar for mobile view
        document.getElementById('mobileSidebarToggle').addEventListener('click', () => {
            document.querySelector('.sidebar').classList.toggle('active');
        });

        // Function to calculate and display comparison
        function updateReasonComparison() {
            const numNeededInput = document.getElementById('num_needed');
            const selectedReasonInput = document.querySelector('input[name="reason_type"]:checked');
            const comparisonDisplay = document.getElementById('comparison-display');
            
            if (!numNeededInput || !selectedReasonInput || !comparisonDisplay) {
                return;
            }

            const numNeeded = parseInt(numNeededInput.value) || 0;
            const selectedReason = selectedReasonInput.value;
            
            if (numNeeded <= 0 || !selectedReason) {
                comparisonDisplay.innerHTML = '';
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
                
                // Show detailed comparison
                comparisonText = `Names: ${repNames}, Applicants: ${appNames}, Needed: ${numNeeded}`;
                percentage = Math.min(100, Math.round((Math.min(repNames, appNames) / numNeeded) * 100));
            } 
            else if (selectedReason === 'manning') {
                reasonCount = Array.from(document.querySelectorAll('input[name="manning_spec[]"]'))
                    .filter(input => input.value.trim() !== '').length;
                
                comparisonText = `Manning Specs: ${reasonCount}, Needed: ${numNeeded}`;
                percentage = Math.min(100, Math.round((reasonCount / numNeeded) * 100));
            } 
            else if (selectedReason === 'others_reason') {
                reasonCount = Array.from(document.querySelectorAll('input[name="others_reason_spec[]"]'))
                    .filter(input => input.value.trim() !== '').length;
                
                comparisonText = `Reasons: ${reasonCount}, Needed: ${numNeeded}`;
                percentage = Math.min(100, Math.round((reasonCount / numNeeded) * 100));
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
            
            comparisonDisplay.innerHTML = displayHTML;
        }

        // Initialize comparison display
        document.addEventListener('DOMContentLoaded', () => {
            updateReasonComparison();
        });
    </script>
</body>
</html>