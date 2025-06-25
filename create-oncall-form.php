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
      gap: 6px;
      margin-bottom: 6px;
    }
    /* Compact form styles */
    .compact-input {
      padding: 0.4rem 0.6rem;
      font-size: 0.875rem;
      height: 2rem;
    }
    .compact-label {
      font-size: 0.875rem;
      margin-bottom: 0.2rem;
    }
    .compact-btn {
      padding: 0.3rem 0.6rem;
      font-size: 0.8125rem;
      margin-bottom: 8px;
    }
    .compact-section {
      gap: 0.8rem;
    }
    .form-group {
      margin-bottom: 0.8rem;
    }
    .checkbox-group {
      margin-bottom: 0.4rem;
    }
    .section-title {
      font-weight: 600;
      margin: 0.8rem 0 0.5rem;
      font-size: 0.95rem;
      color: #333;
      padding-bottom: 0.2rem;
      border-bottom: 1px solid #eee;
    }
    .delete-field-btn {
      height: 2rem;
      min-width: 2rem;
      display: flex;
      align-items: center;
      justify-content: center;
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
                       class="flex items-center space-x-2 p-2 rounded bg-white text-orange-600">
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
        <div class="flex items-center justify-between p-3">
          <div class="flex items-center">
            <button id="mobileSidebarToggle" class="md:hidden mr-3">
              <i class="fas fa-bars text-gray-600"></i>
            </button>
            <h1 class="text-lg font-semibold text-gray-800">Create PRF-On Call Form</h1>
          </div>
        </div>
      </header>

      <main class="flex-1 overflow-y-auto p-4">
        <div class="max-w-6xl mx-auto">
          <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
            <form action="#" class="grid grid-cols-1 md:grid-cols-2 gap-4 compact-section">
              <!-- Left Column -->
              <div class="space-y-3">
                <div class="form-group">
                  <label class="block compact-label">PRF No.</label>
                  <input type="text" name="prf_no" class="w-full compact-input border rounded"/>
                </div>

                <div class="form-group">
                  <label class="block compact-label">Position Title</label>
                  <input type="text" name="position_title" class="w-full compact-input border rounded"/>
                </div>

                <div class="form-group">
                  <label class="block compact-label">Reports to</label>
                  <input type="text" name="reports_to" class="w-full compact-input border rounded"/>
                </div>

                <div class="form-group">
                  <label class="block compact-label">Job Level</label>
                  <input type="text" name="job_level" class="w-full compact-input border rounded"/>
                </div>

                <div class="section-title">Reason for Request:</div>
                
                <div class="checkbox-group">
                  <div class="flex items-center text-sm">
                    <input type="checkbox" name="replacement" id="replacement" class="mr-2">
                    <label for="replacement">Replacement of:</label>
                  </div>
                  <div id="replacement-fields">
                    <div class="input-with-delete">
                      <input type="text" name="rep_of_name[]" class="w-full compact-input border rounded" placeholder="Specify name">
                      <button type="button" class="delete-field-btn bg-red-500 text-white px-1.5 py-0.5 rounded text-xs hover:bg-red-600">
                        <i class="fas fa-trash-alt"></i>
                      </button>
                    </div>
                  </div>
                  <button type="button" id="addReplacementField" class="bg-blue-500 text-white compact-btn rounded text-sm hover:bg-blue-600">Add name</button>
                </div>

                <div class="checkbox-group">
                  <div class="flex items-center text-sm">
                    <input type="checkbox" name="additional_manning" id="additional_manning" class="mr-2">
                    <label for="additional_manning">Additional Manning (attach approval from BOD):</label>
                  </div>
                  <div id="additional-manning-fields">
                    <div class="input-with-delete">
                      <input type="text" name="additional_manning_specify[]" class="w-full compact-input border rounded" placeholder="Specify">
                      <button type="button" class="delete-field-btn bg-red-500 text-white px-1.5 py-0.5 rounded text-xs hover:bg-red-600">
                        <i class="fas fa-trash-alt"></i>
                      </button>
                    </div>
                  </div>
                  <button type="button" id="addAdditionalManningField" class="bg-blue-500 text-white compact-btn rounded text-sm hover:bg-blue-600">Add manning</button>
                </div>

                <div class="checkbox-group">
                  <div class="flex items-center text-sm">
                    <input type="checkbox" name="others" id="others" class="mr-2">
                    <label for="others">Others, please specify:</label>
                  </div>
                  <div id="others-fields">
                    <div class="input-with-delete">
                      <input type="text" name="others_specify[]" class="w-full compact-input border rounded" placeholder="Specify">
                      <button type="button" class="delete-field-btn bg-red-500 text-white px-1.5 py-0.5 rounded text-xs hover:bg-red-600">
                        <i class="fas fa-trash-alt"></i>
                      </button>
                    </div>
                  </div>
                  <button type="button" id="addOthersField" class="bg-blue-500 text-white compact-btn rounded text-sm hover:bg-blue-600">Add reason</button>
                </div>
              </div>

              <!-- Right Column -->
              <div class="space-y-3">
                <div class="form-group">
                  <label class="block compact-label">Date Required</label>
                  <input type="date" name="date_required" class="w-full compact-input border rounded"/>
                </div>

                <div class="form-group">
                  <label class="block compact-label">Date Revised</label>
                  <input type="date" name="date_revised" class="w-full compact-input border rounded"/>
                </div>

                <div class="form-group">
                  <label class="block compact-label">Number Needed</label>
                  <input type="number" name="number_needed" class="w-full compact-input border rounded"/>
                </div>

                <div class="section-title">POSITION REQUIREMENTS:</div>
                
                <div class="grid grid-cols-[auto_1fr] gap-2 text-sm">
                  <div class="flex items-center">
                    <input type="checkbox" name="laptop" id="laptop" class="mr-2">
                    <label for="laptop">Laptop/Desktop:</label>
                  </div>
                  <input type="number" name="laptop_qty" class="compact-input border rounded">

                  <div class="flex items-center">
                    <input type="checkbox" name="mobile" id="mobile" class="mr-2">
                    <label for="mobile">Mobile Unit:</label>
                  </div>
                  <input type="number" name="mobile_qty" class="compact-input border rounded">

                  <div class="flex items-center">
                    <input type="checkbox" name="phone" id="phone" class="mr-2">
                    <label for="phone">Phone Plan:</label>
                  </div>
                  <input type="number" name="phone_qty" class="compact-input border rounded">

                  <div class="flex items-center">
                    <input type="checkbox" name="office" id="office" class="mr-2">
                    <label for="office">Office/Desk Space:</label>
                  </div>
                  <input type="number" name="office_qty" class="compact-input border rounded">

                  <div class="flex items-center">
                    <input type="checkbox" name="uniform" id="uniform" class="mr-2">
                    <label for="uniform">Uniform:</label>
                  </div>
                  <input type="number" name="uniform_qty" class="compact-input border rounded">

                  <div class="flex items-center">
                    <input type="checkbox" name="table" id="table" class="mr-2">
                    <label for="table">Table:</label>
                  </div>
                  <input type="number" name="table_qty" class="compact-input border rounded">

                  <div class="flex items-center">
                    <input type="checkbox" name="chair" id="chair" class="mr-2">
                    <label for="chair">Chair:</label>
                  </div>
                  <input type="number" name="chair_qty" class="compact-input border rounded">

                  <div class="flex items-center">
                    <input type="checkbox" name="others_req" id="others_req" class="mr-2">
                    <label for="others_req">Others:</label>
                  </div>
                  <div class="col-span-2">
                    <div id="others-req-fields">
                      <div class="input-with-delete">
                        <input type="text" name="others_specify_req[]" class="w-full compact-input border rounded" placeholder="Specify requirement">
                        <button type="button" class="delete-field-btn bg-red-500 text-white px-1.5 py-0.5 rounded text-xs hover:bg-red-600">
                          <i class="fas fa-trash-alt"></i>
                        </button>
                      </div>
                    </div>
                    <button type="button" id="addOthersReqField" class="bg-blue-500 text-white compact-btn rounded text-sm hover:bg-blue-600">Add requirement</button>
                  </div>
                </div>
              </div>

              <div class="col-span-1 md:col-span-2 mt-4 flex space-x-3">
                <button type="submit" class="bg-orange-600 text-white py-1.5 px-4 rounded text-sm hover:bg-orange-700 flex items-center">
                  <i class="fas fa-save mr-1 text-xs"></i> Save
                </button>
                <button type="reset" class="bg-gray-300 text-gray-700 py-1.5 px-4 rounded text-sm hover:bg-gray-400 flex items-center">
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
    document.getElementById('mobileSidebarToggle').addEventListener('click', () => {
      document.querySelector('.sidebar').classList.toggle('active');
    });

    // Function to create input field with delete button
    function createInputFieldWithDelete(name, placeholder) {
      const wrapperDiv = document.createElement('div');
      wrapperDiv.className = 'input-with-delete';

      const newInput = document.createElement('input');
      newInput.type = 'text';
      newInput.name = name;
      newInput.className = 'w-full compact-input border rounded';
      newInput.placeholder = placeholder;

      const deleteButton = document.createElement('button');
      deleteButton.type = 'button';
      deleteButton.className = 'delete-field-btn bg-red-500 text-white px-1.5 py-0.5 rounded text-xs hover:bg-red-600';
      deleteButton.innerHTML = '<i class="fas fa-trash-alt"></i>';
      deleteButton.style.height = '2rem';
      deleteButton.style.minWidth = '2rem';
      deleteButton.style.display = 'flex';
      deleteButton.style.alignItems = 'center';
      deleteButton.style.justifyContent = 'center';

      deleteButton.addEventListener('click', () => {
        wrapperDiv.remove();
      });

      wrapperDiv.appendChild(newInput);
      wrapperDiv.appendChild(deleteButton);
      return wrapperDiv;
    }

    // Add replacement field
    document.getElementById('addReplacementField').addEventListener('click', () => {
      const replacementFieldsDiv = document.getElementById('replacement-fields');
      replacementFieldsDiv.appendChild(createInputFieldWithDelete('rep_of_name[]', 'Specify name'));
    });

    // Add additional manning field
    document.getElementById('addAdditionalManningField').addEventListener('click', () => {
      const additionalManningFieldsDiv = document.getElementById('additional-manning-fields');
      additionalManningFieldsDiv.appendChild(createInputFieldWithDelete('additional_manning_specify[]', 'Specify'));
    });

    // Add others reason field
    document.getElementById('addOthersField').addEventListener('click', () => {
      const othersFieldsDiv = document.getElementById('others-fields');
      othersFieldsDiv.appendChild(createInputFieldWithDelete('others_specify[]', 'Specify'));
    });

    // Add others requirement field
    document.getElementById('addOthersReqField').addEventListener('click', () => {
      const othersReqFieldsDiv = document.getElementById('others-req-fields');
      othersReqFieldsDiv.appendChild(createInputFieldWithDelete('others_specify_req[]', 'Specify requirement'));
    });

    // Initialize delete buttons for pre-existing fields
    document.querySelectorAll('.delete-field-btn').forEach(button => {
      button.addEventListener('click', function() {
        this.closest('.input-with-delete').remove();
      });
    });
  </script>
</body>
</html>