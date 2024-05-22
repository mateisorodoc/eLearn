<?php
session_start();

header('Content-Type: application/json'); // Ensure JSON response

if (!isset($_SESSION['loggedin']) || !isset($_SESSION['name'])) {
    $response = array("success" => false, "message" => "User not logged in");
    echo json_encode($response);
    exit;
}

$db_host = 'localhost';
$db_user = 'root';
$db_password = 'root';
$db_db = 'eLearn';
$db_port = 8888;

// Create a new mysqli connection
$mysqli = new mysqli($db_host, $db_user, $db_password, $db_db, $db_port);

// Check for connection errors
if ($mysqli->connect_error) {
    $response = array("success" => false, "message" => 'Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
    echo json_encode($response);
    exit;
}

// Get content from POST data
$front_content = $_POST["front"] ?? '';
$back_content = $_POST["back"] ?? '';
$username = $_SESSION['name'];

// Log received POST data and session data for debugging
error_log("POST front_content: " . $front_content);
error_log("POST back_content: " . $back_content);
error_log("Session username: " . $username);

// Prepare and bind the SQL statement
$sql = "DELETE FROM flashcards WHERE front_content = ? AND back_content = ? AND username = ?";
$stmt = $mysqli->prepare($sql);

// Check for errors in preparing the statement
if (!$stmt) {
    $response = array("success" => false, "message" => 'Error in preparing statement: ' . $mysqli->error);
    echo json_encode($response);
    exit;
}

// Bind parameters to the prepared statement
$stmt->bind_param("sss", $front_content, $back_content, $username);

// Execute the statement
if (!$stmt->execute()) {
    $response = array("success" => false, "message" => 'Error executing statement: ' . $stmt->error);
    echo json_encode($response);
    exit;
}

// Check if any rows were affected (i.e., a flashcard was deleted)
if ($stmt->affected_rows > 0) {
    $response = array("success" => true, "message" => "Flashcard deleted successfully");
} else {
    $response = array("success" => false, "message" => "Flashcard not found or user does not have permission");
}

// Close the statement and connection
$stmt->close();
$mysqli->close();

// Send response
echo json_encode($response);
?>
