<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>CREATE PRF-ON CALL FORM</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
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
    .input-with-delete {
      display: flex;
      align-items: center;
      gap: 8px;
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
                       class="flex items-center space-x-2 p-2 rounded hover:bg-white hover:text-orange-600">
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
            <h1 class="text-xl font-semibold text-gray-800">Create PRF-On Call Form</h1>
          </div>
        </div>
      </header>

      <main class="flex-1 overflow-y-auto p-6">
        <div class="max-w-6xl mx-auto">
          <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <form action="#" class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-4">
                <label class="block">PRF No.</label>
                <input type="text" name="prf_no" class="w-full p-2 border rounded-md"/>

                <label class="block">Position Title</label>
                <input type="text" name="position_title" class="w-full p-2 border rounded-md"/>

                <label class="block">Reports to</label>
                <input type="text" name="reports_to" class="w-full p-2 border rounded-md"/>

                <label class="block">Job Level</label>
                <input type="text" name="job_level" class="w-full p-2 border rounded-md"/>

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

                <input type="checkbox" name="additional_manning" id="additional_manning" class="mr-2">
                <label for="additional_manning">Additional Manning (attach approval from BOD):</label>
                <input type="text" name="additional_manning_specify" class="w-full p-2 border rounded-md" placeholder="Specify if necessary">

                <input type="checkbox" name="others" id="others" class="mr-2">
                <label for="others">Others, please specify:</label>
                <input type="text" name="others_specify" class="w-full p-2 border rounded-md">
              </div>

              <div class="space-y-4">
                <label class="block">Date Required</label>
                <input type="date" name="date_required" class="w-full p-2 border rounded-md"/>

                <label class="block">Date Revised</label>
                <input type="date" name="date_revised" class="w-full p-2 border rounded-md"/>

                <label class="block">Number Needed</label>
                <input type="number" name="number_needed" class="w-full p-2 border rounded-md"/>

                <h4 class="font-semibold mt-4">POSITION REQUIREMENTS:</h4>
                <div class="space-y-2">
                  <div><input type="checkbox" name="laptop" id="laptop" class="mr-2"><label for="laptop">Laptop/Desktop:</label><input type="number" name="laptop_qty" class="w-full p-2 border rounded-md"></div>
                  <div><input type="checkbox" name="mobile" id="mobile" class="mr-2"><label for="mobile">Mobile Unit:</label><input type="number" name="mobile_qty" class="w-full p-2 border rounded-md"></div>
                  <div><input type="checkbox" name="phone" id="phone" class="mr-2"><label for="phone">Phone Plan:</label><input type="number" name="phone_qty" class="w-full p-2 border rounded-md"></div>
                  <div><input type="checkbox" name="office" id="office" class="mr-2"><label for="office">Office/Desk Space:</label><input type="number" name="office_qty" class="w-full p-2 border rounded-md"></div>
                  <div><input type="checkbox" name="uniform" id="uniform" class="mr-2"><label for="uniform">Uniform:</label><input type="number" name="uniform_qty" class="w-full p-2 border rounded-md"></div>
                  <div><input type="checkbox" name="table" id="table" class="mr-2"><label for="table">Table:</label><input type="number" name="table_qty" class="w-full p-2 border rounded-md"></div>
                  <div><input type="checkbox" name="chair" id="chair" class="mr-2"><label for="chair">Chair:</label><input type="number" name="chair_qty" class="w-full p-2 border rounded-md"></div>
                  <div><input type="checkbox" name="others_req" id="others_req" class="mr-2"><label for="others_req">Others, please specify:</label><input type="text" name="others_specify_req" class="w-full p-2 border rounded-md"></div>
                </div>
              </div>

              <div class="col-span-1 md:col-span-2 mt-6 flex space-x-4">
                <button type="submit" class="bg-orange-600 text-white py-2 px-6 rounded-md hover:bg-orange-700">Save</button>
                <button type="reset" class="bg-gray-300 text-gray-700 py-2 px-6 rounded-md hover:bg-gray-400">Reset</button>
              </div>
            </form>
          </div>
        </div>
      </main>
    </div>
  </div>

  <script>
    document.getElementById('mobileSidebarToggle').addEventListener('click', () => {
      document.querySelector('.sidebar').classList.toggle('active');
    });

    function createInputFieldWithDelete(name, placeholder) {
      const wrapperDiv = document.createElement('div');
      wrapperDiv.className = 'input-with-delete mb-2';

      const newInput = document.createElement('input');
      newInput.type = 'text';
      newInput.name = name;
      newInput.className = 'w-full p-2 border rounded-md';
      newInput.placeholder = placeholder;

      const deleteButton = document.createElement('button');
      deleteButton.type = 'button';
      deleteButton.className = 'delete-field-btn bg-red-500 text-white px-2 py-1 rounded-md text-sm hover:bg-red-600';
      deleteButton.innerHTML = '<i class="fas fa-trash-alt"></i>';

      deleteButton.addEventListener('click', () => wrapperDiv.remove());

      wrapperDiv.appendChild(newInput);
      wrapperDiv.appendChild(deleteButton);
      return wrapperDiv;
    }

    document.getElementById('addReplacementField').addEventListener('click', () => {
      const replacementFieldsDiv = document.getElementById('replacement-fields');
      replacementFieldsDiv.appendChild(createInputFieldWithDelete('rep_of_name[]', 'Specify name of individual'));
    });

    document.querySelectorAll('.delete-field-btn').forEach(button => {
      button.addEventListener('click', () => {
        button.closest('.input-with-delete').remove();
      });
    });
  </script>
</body>
</html>
