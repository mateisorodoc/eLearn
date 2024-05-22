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

// Get the username from the session
$username = $_SESSION['name'];

// Prepare and execute the SQL statement to fetch flashcards for the user
$sql = "SELECT front_content, back_content FROM flashcards WHERE username = ?";
$stmt = $mysqli->prepare($sql);

// Check for errors in preparing the statement
if (!$stmt) {
    die('Error in preparing statement: ' . $mysqli->error);
}

// Bind parameters to the prepared statement
$stmt->bind_param("s", $username);

// Execute the statement
if (!$stmt->execute()) {
    die('Error executing statement: ' . $stmt->error);
}

// Get the result
$result = $stmt->get_result();
$flashcards = $result->fetch_all(MYSQLI_ASSOC);

// Output the flashcards as JSON
echo json_encode(['flashcards' => $flashcards]);

// Close the statement and connection
$stmt->close();
$mysqli->close();
?>
