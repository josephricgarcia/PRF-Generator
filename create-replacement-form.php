<?php
include 'db.php';


// Initialize variables
$formData = [
    'prf' => '',
    'pos' => '',
    'rep' => '',
    'job' => '',
    'date_req' => '',
    'date_needed' => '',
    'num_needed' => '',
    'replacement' => false,
    'manning' => false,
    'manning_spec' => '',
    'others_reason' => false,
    'others_reason_spec' => '',
    'laptop' => false,
    'laptop_qty' => '',
    'mobile' => false,
    'mobile_qty' => '',
    'phone' => false,
    'phone_qty' => '',
    'office' => false,
    'office_qty' => '',
    'uniform' => false,
    'uniform_qty' => '',
    'table' => false,
    'table_qty' => '',
    'chair' => false,
    'chair_qty' => '',
    'others_requirement' => false,
    'others_requirement_spec' => '',
    'rep_of_name' => [],
    'app_name' => []
];

$errors = [];
$successMessage = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $formData['prf'] = trim($_POST['prf'] ?? '');
    $formData['pos'] = trim($_POST['pos'] ?? '');
    $formData['rep'] = trim($_POST['rep'] ?? '');
    $formData['job'] = trim($_POST['job'] ?? '');
    $formData['date_req'] = $_POST['date_req'] ?? '';
    $formData['date_needed'] = $_POST['date_needed'] ?? '';
    $formData['num_needed'] = $_POST['num_needed'] ?? '';
    
    // Checkboxes
    $formData['replacement'] = isset($_POST['replacement']);
    $formData['manning'] = isset($_POST['manning']);
    $formData['manning_spec'] = trim($_POST['manning_spec'] ?? '');
    $formData['others_reason'] = isset($_POST['others_reason']);
    $formData['others_reason_spec'] = trim($_POST['others_reason_spec'] ?? '');
    
    $formData['laptop'] = isset($_POST['laptop']);
    $formData['laptop_qty'] = $_POST['laptop_qty'] ?? '';
    $formData['mobile'] = isset($_POST['mobile']);
    $formData['mobile_qty'] = $_POST['mobile_qty'] ?? '';
    $formData['phone'] = isset($_POST['phone']);
    $formData['phone_qty'] = $_POST['phone_qty'] ?? '';
    $formData['office'] = isset($_POST['office']);
    $formData['office_qty'] = $_POST['office_qty'] ?? '';
    $formData['uniform'] = isset($_POST['uniform']);
    $formData['uniform_qty'] = $_POST['uniform_qty'] ?? '';
    $formData['table'] = isset($_POST['table']);
    $formData['table_qty'] = $_POST['table_qty'] ?? '';
    $formData['chair'] = isset($_POST['chair']);
    $formData['chair_qty'] = $_POST['chair_qty'] ?? '';
    $formData['others_requirement'] = isset($_POST['others_requirement']);
    $formData['others_requirement_spec'] = trim($_POST['others_requirement_spec'] ?? '');
    
    // Arrays
    $formData['rep_of_name'] = array_filter(array_map('trim', $_POST['rep_of_name'] ?? []));
    $formData['app_name'] = array_filter(array_map('trim', $_POST['app_name'] ?? []));

    // Validate required fields
    if (empty($formData['prf'])) {
        $errors['prf'] = 'PRF No is required';
    }
    if (empty($formData['pos'])) {
        $errors['pos'] = 'Position Title is required';
    }
    if (empty($formData['date_req'])) {
        $errors['date_req'] = 'Date Requested is required';
    }
    if (empty($formData['date_needed'])) {
        $errors['date_needed'] = 'Date Needed is required';
    }
    if (empty($formData['num_needed']) || !is_numeric($formData['num_needed'])) {
        $errors['num_needed'] = 'Valid Number Needed is required';
    }
    
    // If no errors, save to database
    if (empty($errors)) {
        try {
            $pdo->beginTransaction();
            
            // Insert main form
            $stmt = $pdo->prepare("INSERT INTO prf_replacement_forms (
                prf_no, position_title, reports_to, job_level, date_requested, date_needed, number_needed,
                additional_manning, additional_manning_spec, others_reason, others_reason_spec,
                laptop, laptop_qty, mobile, mobile_qty, phone, phone_qty, office, office_qty,
                uniform, uniform_qty, table_req, table_qty, chair, chair_qty,
                others_requirement, others_requirement_spec
            ) VALUES (
                :prf_no, :position_title, :reports_to, :job_level, :date_requested, :date_needed, :number_needed,
                :additional_manning, :additional_manning_spec, :others_reason, :others_reason_spec,
                :laptop, :laptop_qty, :mobile, :mobile_qty, :phone, :phone_qty, :office, :office_qty,
                :uniform, :uniform_qty, :table_req, :table_qty, :chair, :chair_qty,
                :others_requirement, :others_requirement_spec
            )");
            
            $stmt->execute([
                ':prf_no' => $formData['prf'],
                ':position_title' => $formData['pos'],
                ':reports_to' => $formData['rep'],
                ':job_level' => $formData['job'],
                ':date_requested' => $formData['date_req'],
                ':date_needed' => $formData['date_needed'],
                ':number_needed' => $formData['num_needed'],
                ':additional_manning' => $formData['manning'] ? 1 : 0,
                ':additional_manning_spec' => $formData['manning_spec'],
                ':others_reason' => $formData['others_reason'] ? 1 : 0,
                ':others_reason_spec' => $formData['others_reason_spec'],
                ':laptop' => $formData['laptop'] ? 1 : 0,
                ':laptop_qty' => $formData['laptop_qty'] ?: null,
                ':mobile' => $formData['mobile'] ? 1 : 0,
                ':mobile_qty' => $formData['mobile_qty'] ?: null,
                ':phone' => $formData['phone'] ? 1 : 0,
                ':phone_qty' => $formData['phone_qty'] ?: null,
                ':office' => $formData['office'] ? 1 : 0,
                ':office_qty' => $formData['office_qty'] ?: null,
                ':uniform' => $formData['uniform'] ? 1 : 0,
                ':uniform_qty' => $formData['uniform_qty'] ?: null,
                ':table_req' => $formData['table'] ? 1 : 0,
                ':table_qty' => $formData['table_qty'] ?: null,
                ':chair' => $formData['chair'] ? 1 : 0,
                ':chair_qty' => $formData['chair_qty'] ?: null,
                ':others_requirement' => $formData['others_requirement'] ? 1 : 0,
                ':others_requirement_spec' => $formData['others_requirement_spec']
            ]);
            
            $prfId = $pdo->lastInsertId();
            
            // Insert replacement names
            foreach ($formData['rep_of_name'] as $name) {
                $stmt = $pdo->prepare("INSERT INTO replacement_names (prf_id, name) VALUES (:prf_id, :name)");
                $stmt->execute([':prf_id' => $prfId, ':name' => $name]);
            }
            
            // Insert applicant names
            foreach ($formData['app_name'] as $name) {
                $stmt = $pdo->prepare("INSERT INTO applicant_names (prf_id, name) VALUES (:prf_id, :name)");
                $stmt->execute([':prf_id' => $prfId, ':name' => $name]);
            }
            
            $pdo->commit();
            $successMessage = 'PRF Replacement Form submitted successfully!';
            
            // Clear form data after successful submission
            $formData = array_map(function($item) {
                return is_array($item) ? [] : '';
            }, $formData);
            
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CREATE PRF-REPLACEMENT FORM</title>
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
            gap: 8px;
        }
        .error { color: red; font-size: 0.875rem; margin-top: 0.25rem; }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div class="sidebar bg-orange-600 text-white flex-shrink-0">
    <div class="p-3 flex items-center justify-between border-b border-orange-500">
                <div class="flex items-center space-x-2">
                    <img src="images/be-logo.png" alt="Logo" class="w-8 h-8 rounded-xl object-cover ">
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
                    <a href="dashboard.php" 
                       class="flex items-center space-x-2 p-2 rounded hover:bg-white hover:text-orange-600">
                        <i class="fas fa-tachometer-alt w-4"></i>
                        <span class="text-sm">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="view-form.php" 
                       class="flex items-center space-x-2 p-2 rounded hover:bg-white hover:text-orange-600">
                        <i class="fas fa-folder w-4"></i>
                        <span class="text-sm">View PRF File</span>
                    </a>
                </li>
                <li>
                    <a href="create-replacement-form.php" 
                       class="flex items-center space-x-2 p-2 rounded bg-white text-orange-600">
                        <i class="fas fa-file-alt w-4"></i>
                        <span class="text-sm">Create PRF-Replacement</span>
                    </a>
                </li>
                <li>
                    <a href="create-oncall-form.php" 
                       class="flex items-center space-x-2 p-2 rounded hover:bg-white hover:text-orange-600">
                        <i class="fas fa-file-alt w-4"></i>
                        <span class="text-sm">Create PRF-On Call</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</div>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow-sm">
                <div class="flex items-center justify-between p-4">
                    <div class="flex items-center">
                        <button id="mobileSidebarToggle" class="md:hidden mr-4">
                            <i class="fas fa-bars text-gray-600"></i>
                        </button>
                        <h1 class="text-xl font-semibold text-gray-800">Create PRF-Replacement Form</h1>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6">
                <?php if ($successMessage): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline"><?= htmlspecialchars($successMessage) ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($errors)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Please fix the following errors:</strong>
                        <ul class="mt-2">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <div class="max-w-6xl mx-auto">
                    <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                        <form action="" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <label for="prf" class="block">PRF No:</label>
                                    <input type="text" id="prf" name="prf" value="<?= htmlspecialchars($formData['prf']) ?>" class="w-full p-2 border rounded-md <?= isset($errors['prf']) ? 'border-red-500' : '' ?>" required>
                                    <?php if (isset($errors['prf'])): ?>
                                        <div class="error"><?= $errors['prf'] ?></div>
                                    <?php endif; ?>
                                </div>

                                <div>
                                    <label for="pos" class="block">Position Title:</label>
                                    <input type="text" id="pos" name="pos" value="<?= htmlspecialchars($formData['pos']) ?>" class="w-full p-2 border rounded-md <?= isset($errors['pos']) ? 'border-red-500' : '' ?>" required>
                                    <?php if (isset($errors['pos'])): ?>
                                        <div class="error"><?= $errors['pos'] ?></div>
                                    <?php endif; ?>
                                </div>

                                <div>
                                    <label for="rep" class="block">Reports to:</label>
                                    <input type="text" id="rep" name="rep" value="<?= htmlspecialchars($formData['rep']) ?>" class="w-full p-2 border rounded-md">
                                </div>

                                <div>
                                    <label for="job" class="block">Job Level:</label>
                                    <input type="text" id="job" name="job" value="<?= htmlspecialchars($formData['job']) ?>" class="w-full p-2 border rounded-md">
                                </div>

                                <h4 class="font-semibold mt-4">Reason for Request:</h4>
                                
                                <div class="flex items-center">
                                    <input type="checkbox" name="replacement" id="replacement" class="mr-2" <?= $formData['replacement'] ? 'checked' : '' ?>>
                                    <label for="replacement">Replacement of:</label>
                                </div>
                                
                                <div id="replacement-fields">
                                    <?php if (empty($formData['rep_of_name'])): ?>
                                        <div class="input-with-delete mb-2">
                                            <input type="text" name="rep_of_name[]" class="w-full p-2 border rounded-md" placeholder="Specify name of individual">
                                            <button type="button" class="delete-field-btn bg-red-500 text-white px-2 py-1 rounded-md text-sm hover:bg-red-600">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <?php foreach ($formData['rep_of_name'] as $name): ?>
                                            <div class="input-with-delete mb-2">
                                                <input type="text" name="rep_of_name[]" value="<?= htmlspecialchars($name) ?>" class="w-full p-2 border rounded-md" placeholder="Specify name of individual">
                                                <button type="button" class="delete-field-btn bg-red-500 text-white px-2 py-1 rounded-md text-sm hover:bg-red-600">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                                <button type="button" id="addReplacementField" class="bg-blue-500 text-white px-3 py-1 rounded-md text-sm hover:bg-blue-600 mb-4">Add another name</button>

                                <div>
                                    <label for="app_name" class="block">Applicant Name(s):</label>
                                    <div id="applicant-fields">
                                        <?php if (empty($formData['app_name'])): ?>
                                            <div class="input-with-delete mb-2">
                                                <input type="text" name="app_name[]" class="w-full p-2 border rounded-md" placeholder="Specify name of applicant">
                                                <button type="button" class="delete-field-btn bg-red-500 text-white px-2 py-1 rounded-md text-sm hover:bg-red-600">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        <?php else: ?>
                                            <?php foreach ($formData['app_name'] as $name): ?>
                                                <div class="input-with-delete mb-2">
                                                    <input type="text" name="app_name[]" value="<?= htmlspecialchars($name) ?>" class="w-full p-2 border rounded-md" placeholder="Specify name of applicant">
                                                    <button type="button" class="delete-field-btn bg-red-500 text-white px-2 py-1 rounded-md text-sm hover:bg-red-600">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                    <button type="button" id="addApplicantField" class="bg-blue-500 text-white px-3 py-1 rounded-md text-sm hover:bg-blue-600 mb-4">Add another applicant</button>
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" name="manning" id="manning" class="mr-2" <?= $formData['manning'] ? 'checked' : '' ?>>
                                    <label for="manning">Additional Manning (attach approval from BOD):</label>
                                </div>
                                <input type="text" name="manning_spec" value="<?= htmlspecialchars($formData['manning_spec']) ?>" class="w-full p-2 border rounded-md" placeholder="Specify if necessary">

                                <div class="flex items-center">
                                    <input type="checkbox" name="others_reason" id="others_reason" class="mr-2" <?= $formData['others_reason'] ? 'checked' : '' ?>>
                                    <label for="others_reason">Others, please specify:</label>
                                </div>
                                <input type="text" name="others_reason_spec" value="<?= htmlspecialchars($formData['others_reason_spec']) ?>" class="w-full p-2 border rounded-md">
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label for="date_req" class="block">Date Requested:</label>
                                    <input type="date" id="date_req" name="date_req" value="<?= htmlspecialchars($formData['date_req']) ?>" class="w-full p-2 border rounded-md <?= isset($errors['date_req']) ? 'border-red-500' : '' ?>" required>
                                    <?php if (isset($errors['date_req'])): ?>
                                        <div class="error"><?= $errors['date_req'] ?></div>
                                    <?php endif; ?>
                                </div>

                                <div>
                                    <label for="date_needed" class="block">Date Needed:</label>
                                    <input type="date" id="date_needed" name="date_needed" value="<?= htmlspecialchars($formData['date_needed']) ?>" class="w-full p-2 border rounded-md <?= isset($errors['date_needed']) ? 'border-red-500' : '' ?>" required>
                                    <?php if (isset($errors['date_needed'])): ?>
                                        <div class="error"><?= $errors['date_needed'] ?></div>
                                    <?php endif; ?>
                                </div>

                                <div>
                                    <label for="num_needed" class="block">Number Needed:</label>
                                    <input type="number" id="num_needed" name="num_needed" value="<?= htmlspecialchars($formData['num_needed']) ?>" class="w-full p-2 border rounded-md <?= isset($errors['num_needed']) ? 'border-red-500' : '' ?>" required>
                                    <?php if (isset($errors['num_needed'])): ?>
                                        <div class="error"><?= $errors['num_needed'] ?></div>
                                    <?php endif; ?>
                                </div>

                                <h4 class="font-semibold mt-4">POSITION REQUIREMENTS:</h4>
                                
                                <div class="flex items-center">
                                    <input type="checkbox" name="laptop" id="laptop" class="mr-2" <?= $formData['laptop'] ? 'checked' : '' ?>>
                                    <label for="laptop">Laptop/Desktop:</label>
                                </div>
                                <input type="number" name="laptop_qty" value="<?= htmlspecialchars($formData['laptop_qty']) ?>" class="w-full p-2 border rounded-md">
                                
                                <div class="flex items-center">
                                    <input type="checkbox" name="mobile" id="mobile" class="mr-2" <?= $formData['mobile'] ? 'checked' : '' ?>>
                                    <label for="mobile">Mobile Unit:</label>
                                </div>
                                <input type="number" name="mobile_qty" value="<?= htmlspecialchars($formData['mobile_qty']) ?>" class="w-full p-2 border rounded-md">
                                
                                <div class="flex items-center">
                                    <input type="checkbox" name="phone" id="phone" class="mr-2" <?= $formData['phone'] ? 'checked' : '' ?>>
                                    <label for="phone">Phone Plan:</label>
                                </div>
                                <input type="number" name="phone_qty" value="<?= htmlspecialchars($formData['phone_qty']) ?>" class="w-full p-2 border rounded-md">
                                
                                <div class="flex items-center">
                                    <input type="checkbox" name="office" id="office" class="mr-2" <?= $formData['office'] ? 'checked' : '' ?>>
                                    <label for="office">Office/Desk Space:</label>
                                </div>
                                <input type="number" name="office_qty" value="<?= htmlspecialchars($formData['office_qty']) ?>" class="w-full p-2 border rounded-md">
                                
                                <div class="flex items-center">
                                    <input type="checkbox" name="uniform" id="uniform" class="mr-2" <?= $formData['uniform'] ? 'checked' : '' ?>>
                                    <label for="uniform">Uniform:</label>
                                </div>
                                <input type="number" name="uniform_qty" value="<?= htmlspecialchars($formData['uniform_qty']) ?>" class="w-full p-2 border rounded-md">
                                
                                <div class="flex items-center">
                                    <input type="checkbox" name="table" id="table" class="mr-2" <?= $formData['table'] ? 'checked' : '' ?>>
                                    <label for="table">Table:</label>
                                </div>
                                <input type="number" name="table_qty" value="<?= htmlspecialchars($formData['table_qty']) ?>" class="w-full p-2 border rounded-md">
                                
                                <div class="flex items-center">
                                    <input type="checkbox" name="chair" id="chair" class="mr-2" <?= $formData['chair'] ? 'checked' : '' ?>>
                                    <label for="chair">Chair:</label>
                                </div>
                                <input type="number" name="chair_qty" value="<?= htmlspecialchars($formData['chair_qty']) ?>" class="w-full p-2 border rounded-md">
                                
                                <div class="flex items-center">
                                    <input type="checkbox" name="others_requirement" id="others_requirement" class="mr-2" <?= $formData['others_requirement'] ? 'checked' : '' ?>>
                                    <label for="others_requirement">Others, please specify:</label>
                                </div>
                                <input type="text" name="others_requirement_spec" value="<?= htmlspecialchars($formData['others_requirement_spec']) ?>" class="w-full p-2 border rounded-md">

                                <div class="mt-6 flex space-x-4">
                                    <button type="submit" class="bg-orange-600 text-white py-2 px-6 rounded-md hover:bg-orange-700 flex items-center">
                                        <i class="fas fa-save mr-2"></i> Save
                                    </button>
                                    <button type="reset" class="bg-gray-300 text-gray-700 py-2 px-6 rounded-md hover:bg-gray-400 flex items-center">
                                        <i class="fas fa-undo mr-2"></i> Reset
                                    </button>
                                </div>
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
        function createInputFieldWithDelete(name, placeholder, value = '') {
            const wrapperDiv = document.createElement('div');
            wrapperDiv.className = 'input-with-delete mb-2';

            const newInput = document.createElement('input');
            newInput.type = 'text';
            newInput.name = name;
            newInput.className = 'w-full p-2 border rounded-md';
            newInput.placeholder = placeholder;
            newInput.value = value;

            const deleteButton = document.createElement('button');
            deleteButton.type = 'button';
            deleteButton.className = 'delete-field-btn bg-red-500 text-white px-2 py-1 rounded-md text-sm hover:bg-red-600';
            deleteButton.innerHTML = '<i class="fas fa-trash-alt"></i>';

            deleteButton.addEventListener('click', () => {
                wrapperDiv.remove();
            });

            wrapperDiv.appendChild(newInput);
            wrapperDiv.appendChild(deleteButton);

            return wrapperDiv;
        }

        // Add Replacement Field
        document.getElementById('addReplacementField').addEventListener('click', () => {
            const replacementFieldsDiv = document.getElementById('replacement-fields');
            replacementFieldsDiv.appendChild(createInputFieldWithDelete('rep_of_name[]', 'Specify name of individual'));
        });

        // Add Applicant Field
        document.getElementById('addApplicantField').addEventListener('click', () => {
            const applicantFieldsDiv = document.getElementById('applicant-fields');
            applicantFieldsDiv.appendChild(createInputFieldWithDelete('app_name[]', 'Specify name of applicant'));
        });

        // Initialize delete buttons for pre-existing fields
        document.querySelectorAll('.delete-field-btn').forEach(button => {
            button.addEventListener('click', () => {
                button.closest('.input-with-delete').remove();
            });
        });
    </script>
</body>
</html>