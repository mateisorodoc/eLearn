<?php
session_start();

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
$content = $_POST["content"] ?? '';
$username = $_SESSION['name'];

// Prepare and bind the SQL statement
$sql = "DELETE FROM todo WHERE content = ? AND username = ?";
$stmt = $mysqli->prepare($sql);

// Check for errors in preparing the statement
if (!$stmt) {
    $response = array("success" => false, "message" => 'Error in preparing statement: ' . $mysqli->error);
    echo json_encode($response);
    exit;
}

// Bind parameters to the prepared statement
$stmt->bind_param("ss", $content, $username);

// Execute the statement
if (!$stmt->execute()) {
    $response = array("success" => false, "message" => 'Error executing statement: ' . $stmt->error);
    echo json_encode($response);
    exit;
}

// Check if any rows were affected (i.e., a todo item was deleted)
if ($stmt->affected_rows > 0) {
    $response = array("success" => true, "message" => "Todo item deleted successfully");
} else {
    $response = array("success" => false, "message" => "Todo item not found or user does not have permission");
}

// Close the statement and connection
$stmt->close();
$mysqli->close();

// Send response
echo json_encode($response);
?>
