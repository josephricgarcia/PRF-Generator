<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$mysqli = new mysqli("localhost", "root", "", "prf_db");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: documents-list.php");
    exit();
}

$id = (int)$_GET['id'];

// Fetch document details
$stmt = $mysqli->prepare("SELECT prf_no, file_name, file_type, file_content, upload_date FROM scanned_documents WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $error = "Document with ID $id not found in the database.";
} else {
    $doc = $result->fetch_assoc();
    // Check if file_content is empty
    if (empty($doc['file_content'])) {
        $error = "Document content is missing or corrupted.";
    } else {
        error_log("Fetched document: " . print_r($doc, true));
    }
}
$stmt->close();
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Document</title>
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
            max-height: 600px;
            overflow: auto;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            margin-top: 0.5rem;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .preview-image {
            max-width: 100%;
            max-height: 600px;
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
        .card {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: calc(100vh - 64px);
            padding-top: 1rem;
        }
        .form-container {
            max-width: 48rem;
            width: 100%;
        }
        @media (max-width: 768px) {
            .preview-container {
                max-height: 400px;
            }
            .preview-pdf {
                height: 400px;
            }
        }
        @media print {
            body * {
                visibility: hidden;
            }
            .preview-container, .preview-container * {
                visibility: visible;
                position: absolute;
                top: 0;
                left: 0;
                width: 210mm;
                height: auto;
                max-height: 297mm;
                object-fit: contain;
                border: none;
            }
            .preview-pdf {
                width: 210mm;
                height: 297mm;
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
                        <h1 class="text-lg font-semibold text-gray-800">Preview Document</h1>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto p-4 card">
                <div class="form-container">
                    <?php if (isset($error)): ?>
                        <div class="error"><?php echo htmlspecialchars($error); ?></div>
                    <?php else: ?>
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <div class="space-y-6">
                                <div class="form-group">
                                    <label class="block compact-label">PRF No:</label>
                                    <div class="text-sm text-gray-700"><?php echo htmlspecialchars($doc['prf_no']); ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="block compact-label">File Name:</label>
                                    <div class="text-sm text-gray-700"><?php echo htmlspecialchars($doc['file_name']); ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="block compact-label">File Type:</label>
                                    <div class="text-sm text-gray-700"><?php echo strtoupper(str_replace('image/', '', str_replace('application/', '', $doc['file_type']))); ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="block compact-label">Upload Date:</label>
                                    <div class="text-sm text-gray-700"><?php echo date('d M Y', strtotime($doc['upload_date'])); ?></div>
                                </div>
                                </div   >

                                <div class="section-title">Document Preview</div>
                                <div class="preview-container">
                                    <?php
                                    $viewUrl = "view-document.php?id=" . urlencode($id);
                                    if ($doc['file_type'] === 'application/pdf') {
                                        echo '<iframe src="' . $viewUrl . '" class="preview-pdf" title="Document Preview"></iframe>';
                                        echo '<div class="error" style="display: none;">Error loading PDF. The file might be corrupted or invalid.</div>';
                                    } else {
                                        echo '<img src="' . $viewUrl . '" class="preview-image" alt="Document Preview" onerror="this.style.display=\'none\';this.nextElementSibling.style.display=\'block\';">';
                                        echo '<div class="error" style="display: none;">Error loading image. The file might be corrupted or invalid.</div>';
                                    }
                                    ?>
                                </div>

                                <div class="flex justify-end space-x-3">
                                    <a href="view-documents.php" class="bg-gray-300 text-gray-700 py-2 px-4 rounded text-sm hover:bg-gray-400 flex items-center">
                                        <i class="fas fa-arrow-left mr-1 text-xs"></i> Back
                                    </a>
                                    <a href="upload-document.php?update_id=<?php echo urlencode($id); ?>" class="bg-orange-600 text-white py-2 px-4 rounded text-sm hover:bg-orange-700 flex items-center">
                                        <i class="fas fa-edit mr-1 text-xs"></i> Update
                                    </a>
                                    <button onclick="window.print()" class="bg-blue-600 text-white py-2 px-4 rounded text-sm hover:bg-blue-700 flex items-center">
                                        <i class="fas fa-print mr-1 text-xs"></i> Print
                                    </button>
                                </div>
                            </div>
                        </div>
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
    </script>
</body>
</html>