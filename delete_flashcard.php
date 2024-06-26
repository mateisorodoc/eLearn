<?php
session_start();

if (!isset($_SESSION['loggedin']) || !isset($_SESSION['name'])) {
    die('error: User not logged in');
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
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

// Get content from POST data
$front_content = $_POST["front"] ?? '';
$back_content = $_POST["back"] ?? '';
$username = $_SESSION['name'];

// Prepare and bind the SQL statement
$sql = "DELETE FROM flashcards WHERE front_content = ? AND back_content = ? AND username = ?";
$stmt = $mysqli->prepare($sql);

// Check for errors in preparing the statement
if (!$stmt) {
    die('Error in preparing statement: ' . $mysqli->error);
}

// Bind parameters to the prepared statement
$stmt->bind_param("sss", $front_content, $back_content, $username);

// Execute the statement
if (!$stmt->execute()) {
    die('Error executing statement: ' . $stmt->error);
}

// Check if any rows were affected (i.e., a flashcard was deleted)
if ($stmt->affected_rows > 0) {
    echo 'success';
} else {
    echo 'error';
}

// Close the statement and connection
$stmt->close();
$mysqli->close();
?>
