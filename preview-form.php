<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PERSONNEL REQUISITION FORM (PRF) Preview</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Print-specific styles */
        @media print {
            .sidebar, .print-controls, .form-header {
                display: none;
            }
            body {
                background-color: white;
                margin: 0;
                padding: 0;
            }
            #prfFormContent {
                box-shadow: none;
                border: none;
                padding: 0;
                margin: 0;
                width: 100%;
            }
            .main-content {
                padding: 0;
                margin: 0;
            }
            .form-container {
                max-width: 100%;
                margin: 0;
            }
            body, table {
                font-size: 10pt;
            }
            td {
                padding: 3px;
            }
        }
        
        /* Form styling */
        .prf-table {
            border-collapse: collapse;
            width: 100%;
        }
        .prf-table td {
            border: 1px solid #333;
            padding: 8px;
        }
        .prf-header {
            background-color: #f4a261;
            color: #333;
            text-align: center;
            font-weight: bold;
            font-size: 1.2rem;
        }
        .section-title {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .inner-table {
            width: 100%;
            border-collapse: collapse;
        }
        .inner-table td {
            border: 1px solid #ddd;
            padding: 6px;
        }
        .checkbox-group {
            margin-left: 15px;
            margin-top: 5px;
        }
        ol {
            margin: 5px 0 5px 20px;
        }
        .approval-section td {
            vertical-align: top;
        }
        .signature-line {
            border-bottom: 1px solid #333;
            width: 80%;
            display: inline-block;
            margin-top: 30px;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div class="sidebar bg-orange-600 text-white flex-shrink-0">
    <div class="p-3 flex items-center justify-between border-b border-orange-500">
        <div class="flex items-center space-x-2">
            <!-- Standardized Logo Placeholder -->
                <div class="flex items-center space-x-2">
                    <img src="images/be-logo.png" alt="Logo" class="w-8 h-8 rounded-xl object-cover ">
                    <span class="text-lg font-bold">PRF System</span>
                </div>
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

        <!-- Main Content -->
        <div class="flex-1 p-4 md:p-6 overflow-auto">
            <div class="form-container max-w-4xl mx-auto">
                <!-- Print Controls -->
                <div class="print-controls flex justify-end space-x-4 mb-4">
                    <button id="editForm" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                        <i class="fas fa-edit mr-2"></i>
                        Edit
                    </button>

                    <button id="printForm" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                        <i class="fas fa-print mr-2"></i>
                        Print
                    </button>
                    <button id="downloadPdf" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                        <i class="fas fa-download mr-2"></i>
                        Download PDF
                    </button>
                </div>
                
                <!-- Form Header -->
                <div class="form-header mb-4">
                    <h1 class="text-2xl font-bold text-center text-gray-800">PERSONNEL REQUISITION FORM PREVIEW</h1>
                </div>

                <!-- PRF Form Content -->
                <div id="prfFormContent" class="w-full bg-white p-6 rounded shadow-md">
                    <table class="prf-table">
                        <!-- Header -->
                        <tr>
                            <td colspan="2" class="prf-header">PERSONNEL REQUISITION FORM (PRF)</td>
                        </tr>
                        
                        <!-- PRF Details -->
                        <tr>
                            <td style="width: 50%;">PRF No.: <strong>PRF-2025-017</strong></td>
                            <td style="width: 50%;">Date Requested: <strong>February 17, 2025</strong></td>
                        </tr>
                        <tr>
                            <td>Position Title: <strong>Housekeeping Coordinator</strong></td>
                            <td>Date Needed: <strong>February 28, 2025</strong></td>
                        </tr>
                        <tr>
                            <td>Reports to: <strong>Housekeeping Head</strong></td>
                            <td>Number Needed: <strong>1 housekeeping coordinator</strong></td>
                        </tr>
                        
                        <!-- Reason for Request -->
                        <tr>
                            <td colspan="2" class="section-title">Reason for Request:</td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class="checkbox-group">
                                    <label class="block">
                                        <input type="checkbox" name="reason" value="replacement" class="mr-2">
                                        Replacement of Jennelyn Curay
                                    </label>
                                    <label class="block mt-1">
                                        <input type="checkbox" name="reason" value="additional" class="mr-2">
                                        Additional Manning (attach approval from BOD)
                                    </label>
                                    <label class="block mt-1">
                                        <input type="checkbox" name="reason" value="others" class="mr-2">
                                        Others, please specify: <span class="ml-1 italic text-gray-600">________________________</span>
                                    </label>
                                </div>
                            </td>
                        </tr>
                        
                        <!-- Position Requirements -->
                        <tr>
                            <td colspan="2" class="section-title">POSITION REQUIREMENTS:</td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <table class="inner-table">
                                    <tr>
                                        <td style="width: 70%;">Laptop/Desktop</td>
                                        <td style="width: 30%;"><input type="checkbox"></td>
                                    </tr>
                                    <tr>
                                        <td>Mobile Unit</td>
                                        <td><input type="checkbox"></td>
                                    </tr>
                                    <tr>
                                        <td>Phone Plan</td>
                                        <td><input type="checkbox"></td>
                                    </tr>
                                    <tr>
                                        <td>Office/Desk Space</td>
                                        <td><input type="checkbox"></td>
                                    </tr>
                                    <tr>
                                        <td>Uniform</td>
                                        <td><input type="checkbox"></td>
                                    </tr>
                                    <tr>
                                        <td>Table</td>
                                        <td><input type="checkbox"></td>
                                    </tr>
                                    <tr>
                                        <td>Chair</td>
                                        <td><input type="checkbox"></td>
                                    </tr>
                                    <tr>
                                        <td>Others</td>
                                        <td><input type="checkbox"> <span class="ml-1 italic text-gray-600">Specify: ________________</span></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        
                        <!-- Job Description -->
                        <tr>
                            <td colspan="2" class="section-title">Generic Job Description/Specifications (please attach official Job Description of position):</td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <ol class="list-decimal pl-5">
                                    <li class="mb-2">Responsible for the smooth and efficient manning of the housekeeping office by providing accurate information to hotel guest. To provide clerical administrative support to all housekeeping staff.</li>
                                    <li class="mb-2">Time management and organizational skills</li>
                                    <li class="mb-2">Ability to handle stressful situation with calm and professional approach.</li>
                                    <li>Basic understanding of housekeeping operation and cleaning equipment</li>
                                </ol>
                            </td>
                        </tr>
                        
                        <!-- Qualifications -->
                        <tr>
                            <td colspan="2" class="section-title">Qualifications/Requirements:</td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <table class="inner-table">
                                    <tr>
                                        <td style="width: 30%;">Education</td>
                                        <td style="width: 70%;">Must be a graduate or 2 years course of any related business course.</td>
                                    </tr>
                                    <tr>
                                        <td>Preferable Experience</td>
                                        <td>preferably with 1-2 years experience as Housekeeping Coordinator</td>
                                    </tr>
                                    <tr>
                                        <td>Age Requirement/Physical Attributes</td>
                                        <td>20-25 years old</td>
                                    </tr>
                                    <tr>
                                        <td>Others</td>
                                        <td><span class="italic text-gray-600">________________________________________________</span></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        
                        <!-- Approvals -->
                        <tr>
                            <td colspan="2" class="section-title">APPROVALS</td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <table class="w-full approval-section">
                                    <tr>
                                        <td style="width: 50%;">
                                            <p>Requested by:</p>
                                            <p class="font-bold">PHILIP JAN YUANGAN</p>
                                            <div class="signature-line"></div>
                                            <p>Signature over Printed Name/Date</p>
                                        </td>
                                        <td style="width: 50%;">
                                            <p>Approved by:</p>
                                            <div class="signature-line"></div>
                                            <p>Department Head and/or BOD/Date</p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        
                        <!-- HR Section -->
                        <tr>
                            <td colspan="2" class="section-title">HUMAN RESOURCES (Requesters: Do not write beyond this point)</td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <table class="inner-table">
                                    <tr>
                                        <td style="width: 50%;">HRD Approval</td>
                                        <td style="width: 50%;">Plantilla Details</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="signature-line"></div>
                                            <p>Signature over Printed Name/Date</p>
                                        </td>
                                        <td>Within Budget? 
                                            <input type="checkbox" class="ml-2 mr-1"> Yes 
                                            <input type="checkbox" class="ml-2 mr-1"> No
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>DATE RECEIVED AT HRD:</td>
                                        <td>Current Plantilla Count for Position: <span class="italic text-gray-600">________</span></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Form Footer -->
                <div class="mt-6 text-center text-sm text-gray-500">
                    <p>© 2025 PRF System | Personnel Requisition Form</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        document.getElementById('printForm').addEventListener('click', () => {
            window.print();
        });

        document.getElementById('downloadPdf').addEventListener('click', () => {
            const element = document.getElementById('prfFormContent');
            const opt = {
                margin: 0.5,
                filename: 'Personnel_Requisition_Form.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
            };
            html2pdf().set(opt).from(element).save();
        });
    </script>
</body>
</html>