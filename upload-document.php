<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'db.php'; // Include database connection

// Create scanned_documents table if it doesn't exist
$createTableQuery = "CREATE TABLE IF NOT EXISTS scanned_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prf_no VARCHAR(50) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(50) NOT NULL,
    upload_date DATETIME NOT NULL
)";
$conn->query($createTableQuery);

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $prf = htmlspecialchars($_POST['prf']);
    $uploadDir = 'Uploads/';
    $error = '';
    $success = '';

    // Create uploads directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Handle file upload
    if (isset($_FILES['document']) && $_FILES['document']['error'] == 0) {
        $file = $_FILES['document'];
        $fileName = basename($file['name']);
        $fileType = mime_content_type($file['tmp_name']);
        $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $uniqueFileName = uniqid('doc_') . '.' . $fileExt;
        $filePath = $uploadDir . $uniqueFileName;

        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($file['tmp_name'], $filePath)) {
                // Insert into database
                $stmt = $conn->prepare("INSERT INTO scanned_documents (prf_no, file_name, file_path, file_type, upload_date) VALUES (?, ?, ?, ?, NOW())");
                $stmt->bind_param("ssss", $prf, $fileName, $filePath, $fileType);
                
                if ($stmt->execute()) {
                    $success = "Document uploaded successfully!";
                } else {
                    $error = "Error saving to database: " . $stmt->error;
                    unlink($filePath); // Remove file if database insertion fails
                }
                $stmt->close();
            } else {
                $error = "Error moving uploaded file.";
            }
        } else {
            $error = "Invalid file type. Only JPEG, PNG, and PDF are allowed.";
        }
    } else {
        $error = "No file uploaded.";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan Document</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar {
            transition: all 0.3s ease;
            min-width: 200px;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                overflow: hidden;
                position: absolute;
                z-index: 100;
                height: 100%;
            }
            .sidebar.active {
                width: 200px;
            }
            .main-content {
                width: 100%;
            }
        }
        .compact-input {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            height: 2.5rem;
            width: 100%;
        }
        .compact-label {
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .section-title {
            font-weight: 600;
            margin: 1rem 0 0.75rem;
            font-size: 1rem;
            color: #333;
            padding-bottom: 0.25rem;
            border-bottom: 1px solid #eee;
        }
        .preview-container {
            max-width: 100%;
            max-height: 400px;
            overflow: auto;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            margin-top: 0.5rem;
            display: none;
        }
        .preview-image {
            max-width: 100%;
            height: auto;
        }
        .preview-pdf {
            width: 100%;
            height: 400px;
        }
        .error {
            color: #991b1b;
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }
        .success {
            color: #166534;
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }
        .card {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 64px); /* Adjust for header height */
        }
        .form-container {
            max-width: 32rem; /* 512px */
            width: 100%;
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

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden main-content">
            <!-- Top Navigation -->
            <header class="bg-white shadow-sm">
                <div class="flex items-center justify-between p-3">
                    <div class="flex items-center">
                        <button id="mobileSidebarToggle" class="md:hidden mr-3">
                            <i class="fas fa-bars text-gray-600"></i>
                        </button>
                        <h1 class="text-lg font-semibold text-gray-800">Scan Document</h1>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto p-4 card">
                <div class="form-container">
                    <?php if (isset($success)): ?>
                        <div class="success"><?php echo htmlspecialchars($success); ?></div>
                    <?php elseif (isset($error)): ?>
                        <div class="error"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">
                            <div class="form-group">
                                <label for="prf" class="block compact-label">PRF No:</label>
                                <input type="text" id="prf" name="prf" class="compact-input border rounded focus:outline-none focus:border-orange-600" required>
                            </div>

                            <div class="section-title">Upload Document</div>
                            <div class="form-group">
                                <label for="document" class="block compact-label">Select Document (PDF, JPEG, PNG):</label>
                                <input type="file" id="document" name="document" accept=".pdf,.jpg,.jpeg,.png" class="compact-input border rounded focus:outline-none focus:border-orange-600">
                            </div>

                            <div class="section-title">Preview</div>
                            <div id="previewContainer" class="preview-container">
                                <img id="previewImage" class="preview-image" alt="Document Preview">
                                <iframe id="previewPDF" class="preview-pdf" style="display: none;"></iframe>
                            </div>

                            <div class="flex justify-end space-x-3">
                                <button type="submit" class="bg-orange-600 text-white py-2 px-4 rounded text-sm hover:bg-orange-700 flex items-center">
                                    <i class="fas fa-save mr-1 text-xs"></i> Save
                                </button>
                                <button type="reset" class="bg-gray-300 text-gray-700 py-2 px-4 rounded text-sm hover:bg-gray-400 flex items-center">
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
        // Toggle sidebar on mobile
        document.getElementById('mobileSidebarToggle').addEventListener('click', () => {
            document.querySelector('.sidebar').classList.toggle('active');
        });

        document.getElementById('sidebarToggle').addEventListener('click', () => {
            document.querySelector('.sidebar').classList.toggle('active');
        });

        // Handle file upload preview
        const fileInput = document.getElementById('document');
        const previewContainer = document.getElementById('previewContainer');
        const previewImage = document.getElementById('previewImage');
        const previewPDF = document.getElementById('previewPDF');

        fileInput.addEventListener('change', () => {
            const file = fileInput.files[0];
            if (file) {
                const fileType = file.type;
                const reader = new FileReader();

                reader.onload = (e) => {
                    if (fileType === 'application/pdf') {
                        previewPDF.src = e.target.result;
                        previewPDF.style.display = 'block';
                        previewImage.style.display = 'none';
                    } else {
                        previewImage.src = e.target.result;
                        previewImage.style.display = 'block';
                        previewPDF.style.display = 'none';
                    }
                    previewContainer.style.display = 'block';
                };

                reader.readAsDataURL(file);
            } else {
                previewContainer.style.display = 'none';
            }
        });

        // Clear preview when resetting form
        document.querySelector('button[type="reset"]').addEventListener('click', () => {
            previewContainer.style.display = 'none';
        });
    </script>
</body>
</html>