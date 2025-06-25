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
        /* Flex container for input and delete button */
        .input-with-delete {
            display: flex;
            align-items: center;
            gap: 8px; /* Space between input and button */
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div class="sidebar bg-orange-600 text-white w-70 flex-shrink-0">
            <div class="p-4 flex items-center justify-between border-b border-orange-500">
                <div class="flex items-center space-x-2">
                    <img src="images/be-logo.png" alt="Logo" class="w-8 h-8 rounded-xl object-cover ">
                    <span class="text-lg font-bold">PRF System</span>
                </div>
                <button id="sidebarToggle" class="md:hidden">
                    <i class="fas fa-times"></i>
                </button>
            </div>
                <nav class="p-4">
                    <div class="mb-8">
                        <h3 class="text-orange-400 uppercase text-xs font-semibold mb-4">Main Menu</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="dashboard.php" class="flex items-center space-x-2 p-2 rounded hover:bg-white hover:text-orange-600">
                            <i class="fas fa-tachometer-alt w-5"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="view-form.php" class="flex items-center space-x-2 p-2 rounded hover:bg-white hover:text-orange-600">
                            <i class="fas fa-folder w-5"></i>
                            <span>View PRF File</span>
                        </a>
                    </li>

                    <li>
                        <a href="create-replacement-form.php" class="flex items-center space-x-2 p-2 rounded bg-white text-orange-600">
                            <i class="fas fa-file-alt w-5"></i>
                            <span>Create PRF-Replacement Form</span>
                        </a>
                    </li>

                    <li>
                        <a href="create-oncall-form.php" class="flex items-center space-x-2 p-2 rounded hover:bg-white hover:text-orange-600">
                            <i class="fas fa-file-alt w-5"></i>
                            <span>Create PRF-On Call Form</span>
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
                <div class="max-w-6xl mx-auto">
                    <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                        <form action="#" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <label for="prf" class="block">PRF No:</label>
                                <input type="text" id="prf" name="prf" class="w-full p-2 border rounded-md" required>

                                <label for="pos" class="block">Position Title:</label>
                                <input type="text" id="pos" name="pos" class="w-full p-2 border rounded-md" required>

                                <label for="rep" class="block">Reports to:</label>
                                <input type="text" id="rep" name="rep" class="w-full p-2 border rounded-md">

                                <label for="job" class="block">Job Level:</label>
                                <input type="text" id="job" name="job" class="w-full p-2 border rounded-md">

                                <h4 class="font-semibold mt-4">Reason for Request:</h4>
                                <input type="checkbox" name="replacement" id="replacement" class="mr-2">
                                <label for="replacement">Replacement of:</label>
                                <div id="replacement-fields">
                                    <div class="input-with-delete mb-2">
                                        <input type="text" name="rep_of_name[]" class="w-full p-2 border rounded-md" placeholder="Specify name of individual">
                                        <button type="button" class="delete-field-btn bg-red-500 text-white px-2 py-1 rounded-md text-sm hover:bg-red-600">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="button" id="addReplacementField" class="bg-blue-500 text-white px-3 py-1 rounded-md text-sm hover:bg-blue-600 mb-4">Add another name</button>

                                <label for="app_name" class="block">Applicant Name(s):</label>
                                <div id="applicant-fields">
                                    <div class="input-with-delete mb-2">
                                        <input type="text" name="app_name[]" class="w-full p-2 border rounded-md" placeholder="Specify name of applicant">
                                        <button type="button" class="delete-field-btn bg-red-500 text-white px-2 py-1 rounded-md text-sm hover:bg-red-600">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="button" id="addApplicantField" class="bg-blue-500 text-white px-3 py-1 rounded-md text-sm hover:bg-blue-600 mb-4">Add another applicant</button> <br>


                                <input type="checkbox" name="manning" id="manning" class="mr-2">
                                <label for="manning">Additional Manning (attach approval from BOD):</label>
                                <input type="text" class="w-full p-2 border rounded-md" placeholder="Specify if necessary">

                                <input type="checkbox" name="others" id="others" class="mr-2">
                                <label for="others">Others, please specify:</label>
                                <input type="text" class="w-full p-2 border rounded-md">
                            </div>

                            <div class="space-y-4">
                                <label for="date_req" class="block">Date Requested:</label>
                                <input type="date" id="date_req" name="date_req" class="w-full p-2 border rounded-md" required>

                                <label for="date_needed" class="block">Date Needed:</label>
                                <input type="date" id="date_needed" name="date_needed" class="w-full p-2 border rounded-md" required>

                                <label for="num_needed" class="block">Number Needed:</label>
                                <input type="number" id="num_needed" name="num_needed" class="w-full p-2 border rounded-md" required>

                                <h4 class="font-semibold mt-4">POSITION REQUIREMENTS:</h4>
                                <input type="checkbox" name="laptop" id="laptop" class="mr-2">
                                <label for="laptop">Laptop/Desktop:</label>
                                <input type="number" class="w-full p-2 border rounded-md">

                                <input type="checkbox" name="mobile" id="mobile" class="mr-2">
                                <label for="mobile">Mobile Unit:</label>
                                <input type="number" class="w-full p-2 border rounded-md">

                                <input type="checkbox" name="phone" id="phone" class="mr-2">
                                <label for="phone">Phone Plan:</label>
                                <input type="number" class="w-full p-2 border rounded-md">

                                <input type="checkbox" name="office" id="office" class="mr-2">
                                <label for="office">Office/Desk Space:</label>
                                <input type="number" class="w-full p-2 border rounded-md">

                                <input type="checkbox" name="uniform" id="uniform" class="mr-2">
                                <label for="uniform">Uniform:</label>
                                <input type="number" class="w-full p-2 border rounded-md">

                                <input type="checkbox" name="table" id="table" class="mr-2">
                                <label for="table">Table:</label>
                                <input type="number" class="w-full p-2 border rounded-md">

                                <input type="checkbox" name="chair" id="chair" class="mr-2">
                                <label for="chair">Chair:</label>
                                <input type="number" class="w-full p-2 border rounded-md">

                                <input type="checkbox" name="others" id="others" class="mr-2">
                                <label for="others">Others, please specify:</label>
                                <input type="text" class="w-full p-2 border rounded-md">

                                <div class="mt-6 flex space-x-4">
                                    <button type="submit" class="bg-orange-600 text-white py-2 px-6 rounded-md hover:bg-orange-700">Save</button>
                                    <button type="reset" class="bg-gray-300 text-gray-700 py-2 px-6 rounded-md hover:bg-gray-400">Reset</button>
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
        function createInputFieldWithDelete(name, placeholder) {
            const wrapperDiv = document.createElement('div');
            wrapperDiv.className = 'input-with-delete mb-2'; // Tailwind classes for spacing and layout

            const newInput = document.createElement('input');
            newInput.type = 'text';
            newInput.name = name;
            newInput.className = 'w-full p-2 border rounded-md';
            newInput.placeholder = placeholder;

            const deleteButton = document.createElement('button');
            deleteButton.type = 'button';
            deleteButton.className = 'delete-field-btn bg-red-500 text-white px-2 py-1 rounded-md text-sm hover:bg-red-600';
            deleteButton.innerHTML = '<i class="fas fa-trash-alt"></i>'; // Font Awesome trash icon

            // Add event listener to delete the parent div when button is clicked
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

        // Initialize delete buttons for pre-existing fields (if any)
        document.querySelectorAll('.delete-field-btn').forEach(button => {
            button.addEventListener('click', () => {
                button.closest('.input-with-delete').remove();
            });
        });

    </script>
</body>
</html>