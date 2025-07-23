<?php
include 'db.php'; // Include database connection

// Initialize response array
$response = [
    'success' => false,
    'message' => 'An error occurred.'
];

// Check if the request is POST and required parameters are set
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id']) && isset($_POST['type'])) {
    // Sanitize inputs
    $prf_no = htmlspecialchars($_POST['id']);
    $form_type = htmlspecialchars($_POST['type']);
    
    // Validate form type
    if (!in_array($form_type, ['replacement', 'oncall'])) {
        $response['message'] = 'Invalid form type.';
        echo json_encode($response);
        exit;
    }
    
    // Determine the table based on form type
    $table = ($form_type === 'replacement') ? 'replacement_forms' : 'oncall_forms';
    
    // Prepare the DELETE statement
    $stmt = $conn->prepare("DELETE FROM $table WHERE prf_no = ?");
    $stmt->bind_param("s", $prf_no);
    
    // Execute the statement
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response['success'] = true;
            $response['message'] = 'Form deleted successfully.';
        } else {
            $response['message'] = 'No form found with the specified PRF number.';
        }
    } else {
        $response['message'] = 'Error executing deletion: ' . $stmt->error;
    }
    
    $stmt->close();
} else {
    $response['message'] = 'Invalid request or missing parameters.';
}

$conn->close();

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>