<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'db.php';

// Process form submission for updating document
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $prf = htmlspecialchars(trim($_POST['prf']));
    $updateId = isset($_POST['update_id']) ? (int)$_POST['update_id'] : 0;

    // Validate PRF number
    if (empty($prf) || !preg_match('/^[a-zA-Z0-9\-_]{1,50}$/', $prf)) {
        ?>
        <script>
            alert("Invalid PRF number. Use alphanumeric characters, dashes, or underscores (max 50 characters).");
        </script>
        <?php
    } elseif ($updateId <= 0) {
        ?>
        <script>
            alert("Invalid document ID.");
        </script>
        <?php
    } else {
        // Update existing record
        if (isset($_FILES['document']) && $_FILES['document']['error'] == 0) {
            // New file uploaded, update all fields
            $file = $_FILES['document'];
            $fileName = basename($file['name']);
            $fileType = mime_content_type($file['tmp_name']);
            $fileSize = $file['size'];
            $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
            $maxFileSize = 4 * 1024 * 1024; // 4MB

            if ($fileSize > $maxFileSize) {
                ?>
                <script>
                    alert("File size exceeds the maximum limit of 4MB.");
                </script>
                <?php
            } elseif (in_array($fileType, $allowedTypes)) {
                $fileContent = file_get_contents($file['tmp_name']);
                $stmt = $conn->prepare("UPDATE scanned_documents SET prf_no = ?, file_name = ?, file_type = ?, file_content = ?, upload_date = NOW() WHERE id = ?");
                $null = NULL;
                $stmt->bind_param("sssbi", $prf, $fileName, $fileType, $null, $updateId);
                $stmt->send_long_data(3, $fileContent);
            } else {
                ?>
                <script>
                    alert("Invalid file type. Only JPEG, PNG, and PDF are allowed.");
                </script>
                <?php
            }
        } else {
            // No new file, update only prf_no
            $stmt = $conn->prepare("UPDATE scanned_documents SET prf_no = ? WHERE id = ?");
            $stmt->bind_param("si", $prf, $updateId);
        }

        if (isset($stmt)) {
            if ($stmt->execute()) {
                ?>
                <script>
                    alert("Document updated successfully!");
                    window.location.href = "view-documents.php";
                </script>
                <?php
            } else {
                ?>
                <script>
                    alert("Error updating database: <?php echo addslashes($stmt->error); ?>");
                </script>
                <?php
            }
            $stmt->close();
        }
    }
}

// Fetch document for editing if update_id is provided
$updateId = isset($_GET['update_id']) ? (int)$_GET['update_id'] : 0;
$updateData = null;
if ($updateId > 0) {
    $stmt = $conn->prepare("SELECT prf_no, file_name, file_type FROM scanned_documents WHERE id = ?");
    $stmt->bind_param("i", $updateId);
    $stmt->execute();
    $result = $stmt->get_result();
    $updateData = $result->fetch_assoc();
    $stmt->close();
} else {
    ?>
    <script>
        alert("No document ID provided.");
    </script>
    <?php
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Document</title>
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
            max-width: 50%;
            max-height: 50%;
            overflow: auto;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            margin-top: 0.5rem;
            display: none;
            justify-content: center;
            align-items: center;
        }
        .preview-image {
            max-width: 70%;
            height: auto;
            object-fit: contain;
        }
        .preview-pdf {
            width: 100%;
            height: 600px;
            border: none;
        }
        .error {
            color: #991b1b;
            font-size: 0.75rem;
            margin-top: 0.25rem;
            padding: 0.5rem;
            border-radius: 0.25rem;
            background-color: #fef2f2;
        }
        .success {
            color: #166534;
            font-size: 0.75rem;
            margin-top: 0.25rem;
            padding: 0.5rem;
            border-radius: 0.25rem;
            background-color: #f0fdf4;
        }
        .card {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            min-height: calc(100vh - 64px);
            padding-top: 1rem;
        }
        .form-container {
            max-width: 100%;
            width: 100%;
        }
        .details-container {
            flex: 1;
            padding-left: 1rem;
        }
        @media (max-width: 768px) {
            .preview-container {
                max-height: 400px;
            }
            .preview-pdf {
                height: 400px;
            }
            .card {
                flex-direction: column;
            }
            .details-container {
                padding-left: 0;
                margin-top: 1rem;
            }
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

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden main-content">
            <!-- Top Navigation -->
            <header class="bg-white shadow-sm">
                <div class="flex items-center justify-between p-3">
                    <div class="flex items-center">
                        <button id="mobileSidebarToggle" class="md:hidden mr-3">
                            <i class="fas fa-bars text-gray-600"></i>
                        </button>
                        <h1 class="text-lg font-semibold text-gray-800">Update Document</h1>
                    </div>
                    <div class="flex space-x-3">
                        <a href="view-documents.php" class="bg-gray-300 text-gray-700 py-2 px-4 rounded text-sm hover:bg-gray-400 flex items-center">
                            <i class="fas fa-arrow-left mr-1 text-xs"></i> Back
                        </a>
                        <?php if ($updateData): ?>
                        <button type="submit" form="updateForm" class="bg-orange-600 text-white py-2 px-4 rounded text-sm hover:bg-orange-700 flex items-center">
                            <i class="fas fa-save mr-1 text-xs"></i> Update
                        </button>
                        <?php endif; ?>
                    </div>
                </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto p-4 card">
                <div class="form-container">
                    <?php if ($updateData): ?>
                        <div class="flex">
                            <div class="preview-container" id="previewContainer">
                                <img id="previewImage" class="preview-image" alt="Document Preview" style="display: none;">
                                <iframe id="previewPDF" class="preview-pdf" style="display: none;" title="Document Preview"></iframe>
                                <div class="error" style="display: none;">Error loading preview. The file might be corrupted or invalid.</div>
                            </div>
                            <div class="details-container">
                                <div class="bg-white rounded-lg shadow-sm p-6">
                                    <form id="updateForm" action="" method="POST" enctype="multipart/form-data" class="space-y-6">
                                        <input type="hidden" name="update_id" value="<?php echo $updateId; ?>">
                                        <div class="form-group">
                                            <label for="prf" class="block compact-label">PRF No:</label>
                                            <input type="text" id="prf" name="prf" class="compact-input border rounded focus:outline-none focus:border-orange-600" value="<?php echo htmlspecialchars($updateData['prf_no']); ?>" required pattern="[a-zA-Z0-9\-_]{1,50}" title="Alphanumeric, dashes, or underscores (max 50 characters)">
                                        </div>
                                        <div class="form-group">
                                            <label for="document" class="block compact-label">Select New Document (JPEG, PNG, PDF, max 4MB):</label>
                                            <input type="file" id="document" name="document" accept="image/jpeg,image/png,application/pdf" class="compact-input border rounded focus:outline-none focus:border-orange-600">
                                        </div>
                                        <div class="form-group">
                                            <label class="block compact-label">Current File Name:</label>
                                            <div class="text-sm text-gray-700"><?php echo htmlspecialchars($updateData['file_name']); ?></div>
                                        </div>
                                        <div class="form-group">
                                            <label class="block compact-label">Current File Type:</label>
                                            <div class="text-sm text-gray-700"><?php echo strtoupper(str_replace('image/', '', str_replace('application/', '', $updateData['file_type']))); ?></div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="error">Document not found.</div>
                    <?php endif; ?>
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
                        previewContainer.querySelector('.error').style.display = 'none';
                    } else {
                        previewImage.src = e.target.result;
                        previewImage.style.display = 'block';
                        previewPDF.style.display = 'none';
                        previewContainer.querySelector('.error').style.display = 'none';
                    }
                    previewContainer.style.display = 'flex';
                };

                reader.onerror = () => {
                    previewContainer.style.display = 'flex';
                    previewImage.style.display = 'none';
                    previewPDF.style.display = 'none';
                    previewContainer.querySelector('.error').style.display = 'block';
                };

                reader.readAsDataURL(file);
            } else {
                previewContainer.style.display = 'none';
            }
        });

        // Show existing document preview
        <?php if ($updateData): ?>
            window.addEventListener('load', () => {
                const viewUrl = "view-document.php?id=<?php echo urlencode($updateId); ?>";
                const fileType = "<?php echo $updateData['file_type']; ?>";
                if (fileType === 'application/pdf') {
                    previewPDF.src = viewUrl;
                    previewPDF.style.display = 'block';
                    previewImage.style.display = 'none';
                    previewContainer.querySelector('.error').style.display = 'none';
                } else {
                    previewImage.src = viewUrl;
                    previewImage.style.display = 'block';
                    previewPDF.style.display = 'none';
                    previewContainer.querySelector('.error').style.display = 'none';
                }
                previewContainer.style.display = 'flex';
            });
        <?php endif; ?>
    </script>
</body>
</html>