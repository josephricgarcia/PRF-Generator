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
                            <a href="create-replacement-form.php" class="flex items-center space-x-2 p-2 rounded hover:bg-white hover:text-orange-600">
                                <i class="fas fa-file-alt w-4"></i>
                                <span class="text-sm">Create PRF-Replacement</span>
                            </a>
                        </li>
                        <li>
                            <a href="create-oncall-form.php" class="flex items-center space-x-2 p-2 rounded hover:bg-white hover:text-orange-600">
                                <i class="fas fa-file-alt w-4"></i>
                                <span class="text-sm">Create PRF-On Call</span>
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
                            <input type="text" class="w-full pl-8 pr-3 py-1.5 text-sm rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="Search PRF Forms...">
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
                                <tr>
                                    <td class="table-cell whitespace-nowrap font-medium text-gray-900">PRF-2023-003</td>
                                    <td class="table-cell whitespace-nowrap text-gray-500">Marketing Specialist</td>
                                    <td class="table-cell whitespace-nowrap text-gray-500">On Call</td>
                                    <td class="table-cell whitespace-nowrap">
                                        <span class="status-badge inline-flex rounded-full bg-red-100 text-red-800">Rejected</span>
                                    </td>
                                    <td class="table-cell whitespace-nowrap text-gray-500">25 Jun 2023</td>
                                    <td class="table-cell whitespace-nowrap font-medium action-links">
                                        <a href="preview-form.php" class="text-orange-600 hover:text-orange-800 view-file">
                                            <i class="fas fa-eye"></i>
                                            <span>View</span>
                                        </a>
                                        <a href="update-form.php" class="text-orange-600 hover:text-orange-800">
                                            <i class="fas fa-edit"></i>
                                            <span>Update</span>
                                        </a>
                                        <a href="#" class="text-orange-600 hover:text-orange-800">
                                            <i class="fas fa-trash"></i>
                                            <span>Delete</span>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="table-cell whitespace-nowrap font-medium text-gray-900">PRF-2023-004</td>
                                    <td class="table-cell whitespace-nowrap text-gray-500">Senior Developer</td>
                                    <td class="table-cell whitespace-nowrap text-gray-500">Replacement</td>
                                    <td class="table-cell whitespace-nowrap">
                                        <span class="status-badge inline-flex rounded-full bg-green-100 text-green-800">Approved</span>
                                    </td>
                                    <td class="table-cell whitespace-nowrap text-gray-500">28 Jun 2023</td>
                                    <td class="table-cell whitespace-nowrap font-medium action-links">
                                        <a href="preview-form.php" class="text-orange-600 hover:text-orange-800 view-file">
                                            <i class="fas fa-eye"></i>
                                            <span>View</span>
                                        </a>
                                        <a href="update-form.php" class="text-orange-600 hover:text-orange-800">
                                            <i class="fas fa-edit"></i>
                                            <span>Update</span>
                                        </a>
                                        <a href="#" class="text-orange-600 hover:text-orange-800">
                                            <i class="fas fa-trash"></i>
                                            <span>Delete</span>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="table-cell whitespace-nowrap font-medium text-gray-900">PRF-2023-005</td>
                                    <td class="table-cell whitespace-nowrap text-gray-500">Project Manager</td>
                                    <td class="table-cell whitespace-nowrap text-gray-500">On Call</td>
                                    <td class="table-cell whitespace-nowrap">
                                        <span class="status-badge inline-flex rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                    </td>
                                    <td class="table-cell whitespace-nowrap text-gray-500">30 Jun 2023</td>
                                    <td class="table-cell whitespace-nowrap font-medium action-links">
                                        <a href="preview-form.php" class="text-orange-600 hover:text-orange-800 view-file">
                                            <i class="fas fa-eye"></i>
                                            <span>View</span>
                                        </a>
                                        <a href="update-form.php" class="text-orange-600 hover:text-orange-800">
                                            <i class="fas fa-edit"></i>
                                            <span>Update</span>
                                        </a>
                                        <a href="#" class="text-orange-600 hover:text-orange-800">
                                            <i class="fas fa-trash"></i>
                                            <span>Delete</span>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="table-cell whitespace-nowrap font-medium text-gray-900">PRF-2023-006</td>
                                    <td class="table-cell whitespace-nowrap text-gray-500">HR Coordinator</td>
                                    <td class="table-cell whitespace-nowrap text-gray-500">Replacement</td>
                                    <td class="table-cell whitespace-nowrap">
                                        <span class="status-badge inline-flex rounded-full bg-blue-100 text-blue-800">In Review</span>
                                    </td>
                                    <td class="table-cell whitespace-nowrap text-gray-500">1 Jul 2023</td>
                                    <td class="table-cell whitespace-nowrap font-medium action-links">
                                        <a href="preview-form.php" class="text-orange-600 hover:text-orange-800 view-file">
                                            <i class="fas fa-eye"></i>
                                            <span>View</span>
                                        </a>
                                        <a href="update-form.php" class="text-orange-600 hover:text-orange-800">
                                            <i class="fas fa-edit"></i>
                                            <span>Update</span>
                                        </a>
                                        <a href="#" class="text-orange-600 hover:text-orange-800">
                                            <i class="fas fa-trash"></i>
                                            <span>Delete</span>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="table-cell whitespace-nowrap font-medium text-gray-900">PRF-2023-007</td>
                                    <td class="table-cell whitespace-nowrap text-gray-500">Financial Analyst</td>
                                    <td class="table-cell whitespace-nowrap text-gray-500">On Call</td>
                                    <td class="table-cell whitespace-nowrap">
                                        <span class="status-badge inline-flex rounded-full bg-green-100 text-green-800">Approved</span>
                                    </td>
                                    <td class="table-cell whitespace-nowrap text-gray-500">3 Jul 2023</td>
                                    <td class="table-cell whitespace-nowrap font-medium action-links">
                                        <a href="preview-form.php" class="text-orange-600 hover:text-orange-800 view-file">
                                            <i class="fas fa-eye"></i>
                                            <span>View</span>
                                        </a>
                                        <a href="update-form.php" class="text-orange-600 hover:text-orange-800">
                                            <i class="fas fa-edit"></i>
                                            <span>Update</span>
                                        </a>
                                        <a href="#" class="text-orange-600 hover:text-orange-800">
                                            <i class="fas fa-trash"></i>
                                            <span>Delete</span>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
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
    </script>
</body>
</html>