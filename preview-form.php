<?php
include 'db.php'; // Include database connection

// Initialize variables
$prf_no = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : '';
$form_type = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : '';
$form_data = [];
$error = '';

// Validate form type
if (!in_array($form_type, ['oncall', 'replacement'])) {
    die('Invalid form type');
}

// Fetch form data based on type and PRF number
if ($prf_no && $form_type) {
    $table = $form_type === 'oncall' ? 'oncall_forms' : 'replacement_forms';
    $stmt = $conn->prepare("SELECT * FROM $table WHERE prf_no = ?");
    $stmt->bind_param("s", $prf_no);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $form_data = $result->fetch_assoc();
    } else {
        $error = "Form not found.";
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
    <title>Preview <?php echo ucfirst($form_type); ?> Form</title>
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
                        <h1 class="text-lg font-semibold text-gray-800">Preview <?php echo ucfirst($form_type); ?> Form</h1>
                    </div>
                    <div class="flex space-x-2">
                        <a href="view-form.php" class="bg-gray-300 text-gray-700 py-1.5 px-4 rounded text-sm hover:bg-gray-400 flex items-center">
                            <i class="fas fa-arrow-left mr-1 text-xs"></i> Back
                        </a>
                        <a href="update-form.php?id=<?php echo urlencode($form_data['prf_no']); ?>&type=<?php echo $form_type; ?>" class="bg-orange-600 text-white py-1.5 px-4 rounded text-sm hover:bg-orange-700 flex items-center">
                            <i class="fas fa-edit mr-1 text-xs"></i> Update
                        </a>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-4">
                <div class="max-w-6xl mx-auto">
                    <?php if ($error): ?>
                        <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?php echo $error; ?></div>
                    <?php elseif ($form_data): ?>
                        <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 compact-section">
                                <!-- Left Column -->
                                <div class="space-y-3">
                                    <div class="form-group">
                                        <label class="block compact-label">PRF No:</label>
                                        <div class="w-full compact-input bg-gray-100 p-2 rounded"><?php echo htmlspecialchars($form_data['prf_no']); ?></div>
                                    </div>

                                    <div class="form-group">
                                        <label class="block compact-label">Position Title:</label>
                                        <div class="w-full compact-input bg-gray-100 p-2 rounded"><?php echo htmlspecialchars($form_data['position_title']); ?></div>
                                    </div>

                                    <div class="form-group">
                                        <label class="block compact-label">Reports to:</label>
                                        <div class="w-full compact-input bg-gray-100 p-2 rounded"><?php echo htmlspecialchars($form_data['reports_to']); ?></div>
                                    </div>

                                    <div class="form-group">
                                        <label class="block compact-label">Job Level:</label>
                                        <div class="w-full compact-input bg-gray-100 p-2 rounded"><?php echo htmlspecialchars($form_data['job_level']); ?></div>
                                    </div>

                                    <div class="section-title">Reason for Request:</div>
                                    
                                    <!-- Replacement of Section -->
                                    <div class="checkbox-group border-l-2 <?php echo $form_data['reason_replacement'] ? 'border-orange-500' : 'border-gray-300'; ?> pl-4 mb-4">
                                        <div class="flex items-center text-sm mb-2">
                                            <input type="radio" name="reason_type" id="replacement" value="replacement" class="mr-2" <?php echo $form_data['reason_replacement'] ? 'checked' : ''; ?> disabled>
                                            <label for="replacement" class="font-medium">Replacement of:</label>
                                        </div>
                                        <div id="replacement-fields" class="ml-6">
                                            <?php
                                            $rep_names = json_decode($form_data['rep_of_name'], true);
                                            if ($form_data['reason_replacement'] && $rep_names && is_array($rep_names)) {
                                                foreach ($rep_names as $name) {
                                                    echo '<div class="input-with-delete">';
                                                    echo '<div class="w-full compact-input bg-gray-100 p-2 rounded">' . htmlspecialchars($name) . '</div>';
                                                    echo '</div>';
                                                }
                                            } else {
                                                echo '<div class="input-with-delete">';
                                                echo '<div class="w-full compact-input bg-gray-100 p-2 rounded"></div>';
                                                echo '</div>';
                                            }
                                            ?>
                                        </div>
                                        <?php if ($form_data['reason_replacement']): ?>
                                            <div class="nested-section">
                                                <label class="block compact-label">Applicant Name(s):</label>
                                                <div id="applicant-fields">
                                                    <?php
                                                    $app_names = json_decode($form_data['applicant_names'], true);
                                                    if ($app_names && is_array($app_names)) {
                                                        foreach ($app_names as $name) {
                                                            echo '<div class="input-with-delete">';
                                                            echo '<div class="w-full compact-input bg-gray-100 p-2 rounded">' . htmlspecialchars($name) . '</div>';
                                                            echo '</div>';
                                                        }
                                                    } else {
                                                        echo '<div class="input-with-delete">';
                                                        echo '<div class="w-full compact-input bg-gray-100 p-2 rounded"></div>';
                                                        echo '</div>';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Additional Manning Section -->
                                    <div class="checkbox-group border-l-2 <?php echo $form_data['reason_manning'] ? 'border-orange-500' : 'border-gray-300'; ?> pl-4 mb-4">
                                        <div class="flex items-center text-sm mb-2">
                                            <input type="radio" name="reason_type" id="manning" value="manning" class="mr-2" <?php echo $form_data['reason_manning'] ? 'checked' : ''; ?> disabled>
                                            <label for="manning" class="font-medium">Additional Manning:</label>
                                        </div>
                                        <div id="manning-fields" class="ml-6">
                                            <?php
                                            $manning_specs = json_decode($form_data['manning_spec'], true);
                                            if ($form_data['reason_manning'] && $manning_specs && is_array($manning_specs)) {
                                                foreach ($manning_specs as $spec) {
                                                    echo '<div class="input-with-delete">';
                                                    echo '<div class="w-full compact-input bg-gray-100 p-2 rounded">' . htmlspecialchars($spec) . '</div>';
                                                    echo '</div>';
                                                }
                                            } else {
                                                echo '<div class="input-with-delete">';
                                                echo '<div class="w-full compact-input bg-gray-100 p-2 rounded"></div>';
                                                echo '</div>';
                                            }
                                            ?>
                                        </div>
                                    </div>

                                    <!-- Others Section -->
                                    <div class="checkbox-group border-l-2 <?php echo $form_data['reason_others'] ? 'border-orange-500' : 'border-gray-300'; ?> pl-4 mb-4">
                                        <div class="flex items-center text-sm mb-2">
                                            <input type="radio" name="reason_type" id="others_reason" value="others_reason" class="mr-2" <?php echo $form_data['reason_others'] ? 'checked' : ''; ?> disabled>
                                            <label for="others_reason" class="font-medium">Others:</label>
                                        </div>
                                        <div id="others-reason-fields" class="ml-6">
                                            <?php
                                            $others_reason_specs = json_decode($form_data['others_reason_spec'], true);
                                            if ($form_data['reason_others'] && $others_reason_specs && is_array($others_reason_specs)) {
                                                foreach ($others_reason_specs as $spec) {
                                                    echo '<div class="input-with-delete">';
                                                    echo '<div class="w-full compact-input bg-gray-100 p-2 rounded">' . htmlspecialchars($spec) . '</div>';
                                                    echo '</div>';
                                                }
                                            } else {
                                                echo '<div class="input-with-delete">';
                                                echo '<div class="w-full compact-input bg-gray-100 p-2 rounded"></div>';
                                                echo '</div>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column -->
                                <div class="space-y-3">
                                    <div class="form-group">
                                        <label class="block compact-label">Date Requested:</label>
                                        <div class="w-full compact-input bg-gray-100 p-2 rounded"><?php echo htmlspecialchars($form_data['date_requested']); ?></div>
                                    </div>

                                    <div class="form-group">
                                        <label class="block compact-label">Date Needed:</label>
                                        <div class="w-full compact-input bg-gray-100 p-2 rounded"><?php echo htmlspecialchars($form_data['date_needed']); ?></div>
                                    </div>

                                    <div class="form-group">
                                        <label for="num_needed" class="block compact-label">Number Needed:</label>
                                        <div class="w-full compact-input bg-gray-100 p-2 rounded"><?php echo htmlspecialchars($form_data['number_needed']); ?></div>
                                        <div id="comparison-display" class="mt-1">
                                            <?php
                                            $num_needed = $form_data['number_needed'];
                                            $reason_count = 0;
                                            $comparison_text = '';
                                            $percentage = 0;
                                            if ($form_data['reason_replacement']) {
                                                $rep_names_count = count(json_decode($form_data['rep_of_name'], true));
                                                $app_names_count = count(json_decode($form_data['applicant_names'], true));
                                                $reason_count = min($rep_names_count, $app_names_count);
                                                $comparison_text = "Names: $rep_names_count, Applicants: $app_names_count, Needed: $num_needed";
                                                $percentage = min(100, round(($reason_count / $num_needed) * 100));
                                            } elseif ($form_data['reason_manning']) {
                                                $reason_count = count(json_decode($form_data['manning_spec'], true));
                                                $comparison_text = "Manning Specs: $reason_count, Needed: $num_needed";
                                                $percentage = min(100, round(($reason_count / $num_needed) * 100));
                                            } elseif ($form_data['reason_others']) {
                                                $reason_count = count(json_decode($form_data['others_reason_spec'], true));
                                                $comparison_text = "Reasons: $reason_count, Needed: $num_needed";
                                                $percentage = min(100, round(($reason_count / $num_needed) * 100));
                                            }
                                            if ($num_needed > 0) {
                                                echo '<div class="comparison-container">';
                                                echo '<div class="comparison-bar">';
                                                echo '<div class="comparison-fill" style="width: ' . $percentage . '%"></div>';
                                                echo '</div>';
                                                echo '<div class="comparison-text">' . $comparison_text . '</div>';
                                                echo '</div>';
                                            }
                                            ?>
                                        </div>
                                        <div id="status-preview" class="mt-1 text-sm">
                                            <?php echo 'Current status: <span class="status-badge ' . (strpos($form_data['status'], 'completed') !== false ? 'status-completed' : 'status-pending') . '">' . ucfirst($form_data['status']) . '</span>'; ?>
                                        </div>
                                    </div>

                                    <div class="section-title">Position Requirements:</div>
                                    
                                    <div class="grid grid-cols-[auto_1fr] gap-2 text-sm">
                                        <div class="flex items-center">
                                            <input type="checkbox" id="laptop" class="mr-2" <?php echo $form_data['requirement_laptop'] ? 'checked' : ''; ?> disabled>
                                            <label for="laptop">Laptop/Desktop:</label>
                                        </div>
                                        <div class="w-full compact-input bg-gray-100 p-2 rounded"><?php echo $form_data['requirement_laptop'] ? htmlspecialchars($form_data['laptop_qty']) : ''; ?></div>
                                        
                                        <div class="flex items-center">
                                            <input type="checkbox" id="mobile" class="mr-2" <?php echo $form_data['requirement_mobile'] ? 'checked' : ''; ?> disabled>
                                            <label for="mobile">Mobile Unit:</label>
                                        </div>
                                        <div class="w-full compact-input bg-gray-100 p-2 rounded"><?php echo $form_data['requirement_mobile'] ? htmlspecialchars($form_data['mobile_qty']) : ''; ?></div>
                                        
                                        <div class="flex items-center">
                                            <input type="checkbox" id="phone" class="mr-2" <?php echo $form_data['requirement_phone'] ? 'checked' : ''; ?> disabled>
                                            <label for="phone">Phone Plan:</label>
                                        </div>
                                        <div class="w-full compact-input bg-gray-100 p-2 rounded"><?php echo $form_data['requirement_phone'] ? htmlspecialchars($form_data['phone_qty']) : ''; ?></div>
                                        
                                        <div class="flex items-center">
                                            <input type="checkbox" id="office" class="mr-2" <?php echo $form_data['requirement_office'] ? 'checked' : ''; ?> disabled>
                                            <label for="office">Office/Desk Space:</label>
                                        </div>
                                        <div class="w-full compact-input bg-gray-100 p-2 rounded"><?php echo $form_data['requirement_office'] ? htmlspecialchars($form_data['office_qty']) : ''; ?></div>
                                        
                                        <div class="flex items-center">
                                            <input type="checkbox" id="uniform" class="mr-2" <?php echo $form_data['requirement_uniform'] ? 'checked' : ''; ?> disabled>
                                            <label for="uniform">Uniform:</label>
                                        </div>
                                        <div class="w-full compact-input bg-gray-100 p-2 rounded"><?php echo $form_data['requirement_uniform'] ? htmlspecialchars($form_data['uniform_qty']) : ''; ?></div>
                                        
                                        <div class="flex items-center">
                                            <input type="checkbox" id="table" class="mr-2" <?php echo $form_data['requirement_table'] ? 'checked' : ''; ?> disabled>
                                            <label for="table">Table:</label>
                                        </div>
                                        <div class="w-full compact-input bg-gray-100 p-2 rounded"><?php echo $form_data['requirement_table'] ? htmlspecialchars($form_data['table_qty']) : ''; ?></div>
                                        
                                        <div class="flex items-center">
                                            <input type="checkbox" id="chair" class="mr-2" <?php echo $form_data['requirement_chair'] ? 'checked' : ''; ?> disabled>
                                            <label for="chair">Chair:</label>
                                        </div>
                                        <div class="w-full compact-input bg-gray-100 p-2 rounded"><?php echo $form_data['requirement_chair'] ? htmlspecialchars($form_data['chair_qty']) : ''; ?></div>
                                        
                                        <div class="flex items-center col-span-2">
                                            <input type="checkbox" id="others_requirement" class="mr-2" <?php echo $form_data['requirement_others'] ? 'checked' : ''; ?> disabled>
                                            <label for="others_requirement">Others:</label>
                                        </div>
                                        <div class="col-span-2">
                                            <div id="others-requirement-fields">
                                                <?php
                                                $others_requirement_specs = json_decode($form_data['others_requirement_spec'], true);
                                                if ($form_data['requirement_others'] && $others_requirement_specs && is_array($others_requirement_specs)) {
                                                    foreach ($others_requirement_specs as $spec) {
                                                        echo '<div class="input-with-delete">';
                                                        echo '<div class="w-full compact-input bg-gray-100 p-2 rounded">' . htmlspecialchars($spec) . '</div>';
                                                        echo '</div>';
                                                    }
                                                } else {
                                                    echo '<div class="input-with-delete">';
                                                    echo '<div class="w-full compact-input bg-gray-100 p-2 rounded"></div>';
                                                    echo '</div>';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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

        document.getElementById('sidebarToggle').addEventListener('click', () => {
            document.querySelector('.sidebar').classList.toggle('active');
        });
    </script>
</body>
</html>