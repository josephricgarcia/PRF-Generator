<?php
include 'db.php'; // Include database connection

// Fetch the 3 latest PRF forms
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
          ORDER BY date DESC
          LIMIT 5";

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
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        .sidebar {
            transition: all 0.3s ease;
            min-width: 200px;
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
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex h-screen overflow-hidden">
        <div class="sidebar bg-orange-600 text-white flex-shrink-0 ">
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
                    <a href="dashboard.php" class="flex items-center space-x-2 p-2 rounded bg-white text-orange-600">
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
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation -->
            <header class="bg-white shadow-sm">
                <div class="flex items-center justify-between p-4">
                    <div class="flex items-center">
                        <button id="mobileSidebarToggle" class="md:hidden mr-4">
                            <i class="fas fa-bars text-gray-600"></i>
                        </button>
                        <h1 class="text-xl font-semibold text-gray-800">Personnel Requisition Form (PRF)</h1>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto p-6">
                <div class="max-w-6xl mx-auto">
                    <!-- Welcome Banner -->
                    <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl p-6 mb-8 text-white">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                            <div>
                                <h2 class="text-xl font-bold mb-2">Welcome back!</h2>
                                <p class="opacity-90 text-sm">Manage your personnel requisition forms efficiently</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                        <a href="view-form.php">
                        <div class="card-hover bg-white rounded-xl shadow-md p-6 transition duration-300 cursor-pointer hover:border-orange-500 border border-transparent">
                            <div class="flex items-center space-x-4">
                                <div class="bg-orange-100 p-3 rounded-full">
                                    <i class="fas fa-folder text-orange-600 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-base">PRF Files</h3>
                                    <p class="text-gray-500 text-sm">Access existing PRF documents</p>
                                </div>
                            </div>
                        </div>
                        </a>
                        <a href="view-documents.php">
                        <div class="card-hover bg-white rounded-xl shadow-md p-6 transition duration-300 cursor-pointer hover:border-orange-500 border border-transparent">
                            <div class="flex items-center space-x-4">
                                <div class="bg-orange-100 p-3 rounded-full">
                                    <i class="fas fa-folder text-orange-600 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-base">Scanned Documents</h3>
                                    <p class="text-gray-500 text-sm">Access existing scanned documents</p>
                                </div>
                            </div>
                        </div>
                        </a>
                        <a href="create-replacement-form.php">
                            <div class="card-hover bg-white rounded-xl shadow-md p-6 transition duration-300 cursor-pointer hover:border-blue-500 border border-transparent">
                                <div class="flex items-center space-x-4">
                                    <div class="bg-green-100 p-3 rounded-full">
                                        <i class="fas fa-file-alt text-green-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-base">Create Replacement Form</h3>
                                        <p class="text-gray-500 text-sm">Generate new personnel requisition</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <a href="create-oncall-form.php">
                            <div class="card-hover bg-white rounded-xl shadow-md p-6 transition duration-300 cursor-pointer hover:border-blue-500 border border-transparent">
                                <div class="flex items-center space-x-4">
                                    <div class="bg-green-100 p-3 rounded-full">
                                        <i class="fas fa-file-alt text-green-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-base">Create On Call Form</h3>
                                        <p class="text-gray-500 text-sm">Generate new on-call personnel requisition</p>
                                    </div>
                                </div>
                            </div>
                        </a>

                        <a href="backup-restore.php">
                            <div class="card-hover bg-white rounded-xl shadow-md p-6 transition duration-300 cursor-pointer hover:border-blue-500 border border-transparent">
                                <div class="flex items-center space-x-4">
                                    <div class="bg-green-100 p-3 rounded-full">
                                        <i class="fas fa-database text-green-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-base">Backup/Restore</h3>
                                        <p class="text-gray-500 text-sm">Backup or restore your PRF data</p>
                                    </div>
                                </div>
                            </div>
                        </a>

                    </div>

                    <!-- Recent PRFs Section -->
                    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
                        <div class="p-6 border-b border-gray-200">
                            <h2 class="text-base font-semibold">Recent PRF Forms</h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PRF No.</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PRF-TYPE</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($forms as $form): 
                                        $statusClass = '';
                                        $statusText = $form['status'];
                                        if (strpos(strtolower($statusText), 'approved') !== false || strpos(strtolower($statusText), 'completed') !== false) {
                                            $statusClass = 'bg-green-100 text-green-800';
                                        } elseif (strpos(strtolower($statusText), 'pending') !== false) {
                                            $statusClass = 'bg-yellow-100 text-yellow-800';
                                        } elseif (strpos(strtolower($statusText), 'rejected') !== false) {
                                            $statusClass = 'bg-red-100 text-red-800';
                                        } elseif (strpos(strtolower($statusText), 'in review') !== false) {
                                            $statusClass = 'bg-blue-100 text-blue-800';
                                        }
                                        $formattedDate = date('d M Y', strtotime($form['date']));
                                    ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($form['prf_no']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($form['file_name']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo ucfirst($form['form_type']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusClass; ?>"><?php echo ucfirst($statusText); ?></span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $formattedDate; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($forms)): ?>
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No PRF forms found</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Mobile sidebar toggle
        const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.sidebar');

        mobileSidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });

        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.remove('active');
        });

        // Card hover effect
        const cards = document.querySelectorAll('.card-hover');
        cards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.classList.add('shadow-lg');
            });
            card.addEventListener('mouseleave', () => {
                card.classList.remove('shadow-lg');
            });
        });
    </script>
</body>
</html>