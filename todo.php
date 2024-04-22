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
$sql = "INSERT INTO todo (creation_time, content, status) VALUES (?, ?, ?)";
$stmt = $mysqli->prepare($sql);

// Check for errors in preparing the statement
if (!$stmt) {
    die('Error in preparing statement: ' . $mysqli->error);
}

// Get content and status from POST data
$content = $_POST["content"] ?? '';
$status = $_POST["status"] ?? 0; // Default status to 0 if not provided
$creation_time = date("Y-m-d H:i:s"); // Use current date and time

// Bind parameters to the prepared statement
$stmt->bind_param("ssi", $creation_time, $content, $status);

// Execute the statement
if (!$stmt->execute()) {
    die('Error executing statement: ' . $stmt->error);
}

// Close the statement and connection
$stmt->close();
$mysqli->close();
?>

