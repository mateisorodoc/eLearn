<?php
$db_host = 'localhost';
$db_user = 'root';
$db_password = 'root';
$db_db = 'eLearn';
$db_port = 8888;

// Create a new mysqli connection
$mysqli = new mysqli($db_host, $db_user, $db_password, $db_db, $db_port);

// Check for connection errors
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

// Prepare and bind the SQL statement
$sql = "DELETE FROM todo WHERE content = ?";
$stmt = $mysqli->prepare($sql);

// Check for errors in preparing the statement
if (!$stmt) {
    die('Error in preparing statement: ' . $mysqli->error);
}

// Get content from POST data
$content = $_POST["content"] ?? '';

// Bind parameters to the prepared statement
$stmt->bind_param("s", $content);

// Execute the statement
if (!$stmt->execute()) {
    die('Error executing statement: ' . $stmt->error);
}

// Close the statement and connection
$stmt->close();
$mysqli->close();

// Send response
$response = array("success" => true, "message" => "Todo item deleted successfully");
echo json_encode($response);
?>
    