<?php
include 'db.php'; // Include database connection

// Fetch data from both tables
$query = "SELECT 
            'replacement' AS form_type, 
            prf_no, 
            position_title AS file_name, 
            status, 
            date_requested AS date 
          FROM replacement_forms
          UNION ALL
          SELECT 
            'oncall' AS form_type, 
            prf_no, 
            position_title AS file_name, 
            status, 
            date_requested AS date 
          FROM oncall_forms
          ORDER BY date DESC";

$result = $conn->query($query);
$forms = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $forms[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRF Forms Viewer</title>
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
            .table-container {
                overflow-x: auto;
            }
            table {
                min-width: 600px;
            }
        }
        .table-cell {
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
        }
        .action-links {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        .action-links a {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }
        .action-links a:hover {
            background-color: rgba(255, 165, 0, 0.1);
            border-color: rgba(255, 165, 0, 0.2);
            transform: translateY(-1px);
        }
        .action-links a i {
            margin-right: 0.25rem;
        }
        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        /* FAB Styles */
        .fab-container {
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            z-index: 1000;
        }
        .fab {
            background-color: #f97316;
            color: white;
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }
        .fab:hover {
            background-color: #ea580c;
            transform: scale(1.1);
        }
        .fab.active {
            transform: rotate(45deg);
        }
        .fab-menu {
            position: absolute;
            bottom: 4.5rem;
            right: 0;
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
            display: none;
            flex-direction: column;
            width: 220px;
            opacity: 0;
            transform: translateY(10px);
            transition: all 0.3s ease;
        }
        .fab-menu.active {
            display: flex;
            opacity: 1;
            transform: translateY(0);
        }
        .fab-menu a {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: #1f2937;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }
        .fab-menu a:hover {
            background-color: #f5f5f5;
            color: #f97316;
            border-left-color: #f97316;
        }
        .fab-menu a i {
            margin-right: 0.75rem;
            width: 1.25rem;
            text-align: center;
        }
        .fab-menu a span {
            font-size: 0.9rem;
        }
        .fab-menu::before {
            content: '';
            position: absolute;
            bottom: -0.5rem;
            right: 1rem;
            width: 0;
            height: 0;
            border-left: 8px solid transparent;
            border-right: 8px solid transparent;
            border-top: 8px solid white;
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
                            <a href="view-form.php" class="flex items-center space-x-2 p-2 rounded bg-white text-orange-600">
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
                <div class="flex items-center justify-between p-3 flex-wrap gap-3">
                    <div class="flex items-center">
                        <button id="mobileSidebarToggle" class="md:hidden mr-3">
                            <i class="fas fa-bars text-gray-600"></i>
                        </button>
                        <h1 class="text-lg font-semibold text-gray-800">PRF Forms</h1>
                    </div>
                    <div class="flex-grow min-w-[180px] max-w-md">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-2">
                                <i class="fas fa-search text-gray-400 text-sm"></i>
                            </span>
                            <input type="text" class="w-full pl-8 pr-3 py-1.5 text-sm rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="Search by PRF No...">
                        </div>
                    </div>
                </div>
            </header>

            <div class="flex-1 overflow-auto p-3">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-4">
                    <div class="overflow-x-auto table-container">
                        <table class="w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="table-cell text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PRF No.</th>
                                    <th class="table-cell text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File Name</th>
                                    <th class="table-cell text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PRF-TYPE</th>
                                    <th class="table-cell text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="table-cell text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="table-cell text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($forms as $form): 
                                    $statusClass = '';
                                    $statusText = $form['status'];
                                    
                                    if (strpos($statusText, 'completed') !== false) {
                                        $statusClass = 'bg-green-100 text-green-800';
                                    } elseif (strpos($statusText, 'pending') !== false) {
                                        $statusClass = 'bg-yellow-100 text-yellow-800';
                                    } elseif (strpos($statusText, 'rejected') !== false) {
                                        $statusClass = 'bg-red-100 text-red-800';
                                    } elseif (strpos($statusText, 'in review') !== false) {
                                        $statusClass = 'bg-blue-100 text-blue-800';
                                    }
                                    
                                    $formattedDate = date('d M Y', strtotime($form['date']));
                                ?>
                                <tr>
                                    <td class="table-cell whitespace-nowrap font-medium text-gray-900"><?php echo htmlspecialchars($form['prf_no']); ?></td>
                                    <td class="table-cell whitespace-nowrap text-gray-500"><?php echo htmlspecialchars($form['file_name']); ?></td>
                                    <td class="table-cell whitespace-nowrap text-gray-500">
                                        <?php echo ucfirst($form['form_type']); ?>
                                    </td>
                                    <td class="table-cell whitespace-nowrap">
                                        <span class="status-badge inline-flex rounded-full <?php echo $statusClass; ?>">
                                            <?php echo ucfirst($statusText); ?>
                                        </span>
                                    </td>
                                    <td class="table-cell whitespace-nowrap text-gray-500"><?php echo $formattedDate; ?></td>
                                    <td class="table-cell whitespace-nowrap font-medium action-links">
                                        <a href="preview-form.php?id=<?php echo urlencode($form['prf_no']); ?>&type=<?php echo $form['form_type']; ?>" class="text-blue-600 hover:text-blue-800 view-file">
                                            <i class="fas fa-eye"></i>
                                            <span>View</span>
                                        </a>
                                        <a href="update-form.php?id=<?php echo urlencode($form['prf_no']); ?>&type=<?php echo $form['form_type']; ?>" class="text-green-600 hover:text-green-800">
                                            <i class="fas fa-pen-to-square"></i>
                                            <span>Update</span>
                                        </a>
                                        <a href="delete-form.php" class="text-red-600 hover:text-red-800 delete-form" data-id="<?php echo htmlspecialchars($form['prf_no']); ?>" data-type="<?php echo htmlspecialchars($form['form_type']); ?>">
                                            <i class="fas fa-trash-can"></i>
                                            <span>Delete</span>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($forms)): ?>
                                <tr>
                                    <td colspan="6" class="table-cell text-center py-4 text-gray-500">No PRF forms found</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Floating Action Button -->
            <div class="fab-container">
                <button id="fabToggle" class="fab">
                    <i class="fas fa-plus"></i>
                </button>
                <div id="fabMenu" class="fab-menu">
                    <a href="create-replacement-form.php">
                        <i class="fas fa-file-alt"></i>
                        <span>Create Replacement Form</span>
                    </a>
                    <a href="create-oncall-form.php">
                        <i class="fas fa-file-alt"></i>
                        <span>Create On Call Form</span>
                    </a>
                    <a href="upload-document.php">
                        <i class="fas fa-upload"></i>
                        <span>Upload Document</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle sidebar on mobile
        document.getElementById('mobileSidebarToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });

        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });

        // Handle FAB toggle
        document.getElementById('fabToggle').addEventListener('click', function() {
            this.classList.toggle('active');
            document.getElementById('fabMenu').classList.toggle('active');
        });

        // Close FAB menu when clicking outside
        document.addEventListener('click', function(event) {
            const fabContainer = document.querySelector('.fab-container');
            if (!fabContainer.contains(event.target)) {
                document.getElementById('fabMenu').classList.remove('active');
                document.getElementById('fabToggle').classList.remove('active');
            }
        });

        // Handle delete button clicks
        document.querySelectorAll('.delete-form').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const prfNo = this.getAttribute('data-id');
                const formType = this.getAttribute('data-type');
                
                if (confirm(`Are you sure you want to delete PRF ${prfNo}?`)) {
                    fetch('delete-form.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `id=${encodeURIComponent(prfNo)}&type=${encodeURIComponent(formType)}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            window.location.reload();
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while deleting the form.');
                    });
                }
            });
        });

        // Search functionality
        const searchInput = document.querySelector('input[type="text"]');
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const prfNo = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
                
                if (prfNo.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>