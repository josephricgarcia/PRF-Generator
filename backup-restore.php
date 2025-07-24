<?php
include 'db.php'; // Include database connection

// Initialize variables
$success = "";
$error = "";

// Set timezone to Asia/Manila
date_default_timezone_set('Asia/Manila');

// Function to backup database
function backupDatabase($conn, $dbname) {
    $tables = ['oncall_forms', 'replacement_forms', 'scanned_documents'];
    $backupFile = 'backup_' . $dbname . '_' . date('Y-m-d_H-i-s') . '.sql';
    $backupContent = "-- PRF System Database Backup\n";
    $backupContent .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
    
    // Set headers for file download
    header('Content-Type: text/sql');
    header('Content-Disposition: attachment; filename="' . $backupFile . '"');
    
    foreach ($tables as $table) {
        // Get table structure
        $result = $conn->query("SHOW CREATE TABLE $table");
        if ($result && $row = $result->fetch_assoc()) {
            $backupContent .= "-- Table structure for $table\n";
            $backupContent .= "DROP TABLE IF EXISTS `$table`;\n";
            $backupContent .= $row['Create Table'] . ";\n\n";
        }
        
        // Get table data
        $result = $conn->query("SELECT * FROM $table");
        if ($result && $result->num_rows > 0) {
            $backupContent .= "-- Data for $table\n";
            while ($row = $result->fetch_assoc()) {
                $columns = array_keys($row);
                $values = array_map(function($value) use ($conn) {
                    return $value === null ? 'NULL' : "'" . $conn->real_escape_string($value) . "'";
                }, array_values($row));
                
                $backupContent .= "INSERT INTO `$table` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $values) . ");\n";
            }
            $backupContent .= "\n";
        }
    }
    
    return $backupContent;
}

// Function to restore database with enhanced error handling
function restoreDatabase($conn, $file) {
    // Validate file
    if (!is_uploaded_file($file)) {
        return "Error: Invalid file upload.";
    }

    // Check file size (limit to 10MB)
    $maxFileSize = 10 * 1024 * 1024; // 10MB in bytes
    if (filesize($file) > $maxFileSize) {
        return "Error: File size exceeds 10MB limit.";
    }
    if (filesize($file) == 0) {
        return "Error: Uploaded file is empty.";
    }

    // Check file extension
    $fileInfo = pathinfo($_FILES['backup_file']['name']);
    $extension = strtolower($fileInfo['extension']);
    if ($extension !== 'sql') {
        return "Error: File must have a .sql extension.";
    }

    // Check MIME type
    $allowedMimeTypes = ['text/plain', 'text/sql', 'application/sql', 'application/octet-stream'];
    $fileType = mime_content_type($file);
    if (!in_array($fileType, $allowedMimeTypes)) {
        return "Error: Invalid file type. Allowed types: .sql (detected: $fileType).";
    }

    // Read the SQL file
    $sql = '';
    $error = '';
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return "Error: Unable to read the uploaded file.";
    }

    foreach ($lines as $lineNum => $line) {
        // Skip comments
        if (preg_match('/^--|^\/\*|^$/', trim($line))) {
            continue;
        }

        $sql .= $line . "\n";

        // Execute if query is complete
        if (preg_match('/;\s*$/', trim($line))) {
            $sql = trim($sql);
            if (!empty($sql)) {
                if (!$conn->multi_query($sql)) {
                    $error .= "Error at line " . ($lineNum + 1) . ": " . $conn->error . "\n";
                } else {
                    // Flush multi-queries
                    do {
                        if ($result = $conn->store_result()) {
                            $result->free();
                        }
                    } while ($conn->next_result());
                }
            }
            $sql = '';
        }
    }

    return $error ?: true;
}

// Process backup request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'backup') {
    $backupContent = backupDatabase($conn, $dbname);
    echo $backupContent;
    exit;
}

// Process restore request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'restore') {
    if (isset($_FILES['backup_file']) && $_FILES['backup_file']['error'] == UPLOAD_ERR_OK) {
        $file = $_FILES['backup_file']['tmp_name'];
        $result = restoreDatabase($conn, $file);
        if ($result === true) {
            $success = "Database restored successfully!";
        } else {
            $error = "Restore failed: " . $result;
        }
    } else {
        $error = "No file uploaded or an error occurred during upload. Error code: " . ($_FILES['backup_file']['error'] ?? 'Unknown');
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup & Restore Database</title>
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
        .card {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
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
                        <h1 class="text-lg font-semibold text-gray-800">Backup & Restore Database</h1>
                    </div>
                    <div class="flex space-x-3">
                        <a href="dashboard.php" class="bg-blue-600 text-white py-1.5 px-4 rounded text-sm hover:bg-blue-700 flex items-center">
                            <i class="fas fa-arrow-left mr-1 text-xs"></i> Back
                        </a>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto p-4">
                <div class="max-w-4xl mx-auto">
                    <!-- Backup Section -->
                    <div class="card">
                        <div class="section-title">Create Database Backup</div>
                        <form action="" method="POST">
                            <input type="hidden" name="action" value="backup">
                            <div class="form-group">
                                <p class="text-sm text-gray-600 mb-4">Click the button below to create a backup of the PRF database. This will download a .sql file containing all data.</p>
                                <button type="submit" class="bg-orange-600 text-white py-2 px-4 rounded text-sm hover:bg-orange-700 flex items-center">
                                    <i class="fas fa-download mr-1 text-xs"></i> Create Backup
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Restore Section -->
                    <div class="card">
                        <div class="section-title">Restore Database</div>
                        <form action="" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="restore">
                            <div class="form-group">
                                <label for="backup_file" class="block compact-label">Select Backup File (.sql):</label>
                                <input type="file" id="backup_file" name="backup_file" accept=".sql" class="compact-input border rounded focus:outline-none focus:border-orange-600" required>
                            </div>
                            <div class="form-group">
                                <p class="text-sm text-gray-600 mb-4">Upload a .sql file to restore the database. Warning: This will overwrite existing data!</p>
                                <button type="submit" class="bg-orange-600 text-white py-2 px-4 rounded text-sm hover:bg-orange-700 flex items-center">
                                    <i class="fas fa-upload mr-1 text-xs"></i> Restore Database
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

        // Display success or error message if set
        <?php if (!empty($success)): ?>
            alert(<?php echo json_encode($success); ?>);
            window.location.href = 'dashboard.php';
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            alert(<?php echo json_encode($error); ?>);
            window.location.href = 'backup-restore.php';
        <?php endif; ?>
    </script>
</body>
</html>