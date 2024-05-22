<?php
session_start(); // Start the session

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
$sql = "INSERT INTO flashcards (creation_time, username, front_content, back_content) VALUES (NOW(), ?, ?, ?)";
$stmt = $mysqli->prepare($sql);

// Check for errors in preparing the statement
if (!$stmt) {
    die('Error in preparing statement: ' . $mysqli->error);
}

// Get content from POST data
$username = $_SESSION['name'] ?? ''; // Retrieve username from session
$front_content = $_POST["front"] ?? '';
$back_content = $_POST["back"] ?? '';
$creation_time = $_POST["creationTime"] ?? '';

// Bind parameters to the prepared statement
$stmt->bind_param("sss", $username, $front_content, $back_content);

// Execute the statement
if (!$stmt->execute()) {
    die('Error executing statement: ' . $stmt->error);
}

// Close the statement and connection
$stmt->close();
$mysqli->close();
?>
