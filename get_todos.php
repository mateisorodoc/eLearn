<?php
session_start(); // Start the session

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

// Get username from session
$username = $_SESSION['name'];

// Prepare the SQL statement
$sql = "SELECT * FROM todo WHERE username = ?";

// Prepare and bind the SQL statement
$stmt = $mysqli->prepare($sql);

// Check for errors in preparing the statement
if (!$stmt) {
    $response = array("success" => false, "message" => 'Error in preparing statement: ' . $mysqli->error);
    echo json_encode($response);
    exit;
}

// Bind parameters to the prepared statement
$stmt->bind_param("s", $username);

// Execute the statement
if (!$stmt->execute()) {
    $response = array("success" => false, "message" => 'Error executing statement: ' . $stmt->error);
    echo json_encode($response);
    exit;
}

// Get the result
$result = $stmt->get_result();

// Initialize an array to store todos
$todos = array();

// Fetch todos and store them in the array
while ($row = $result->fetch_assoc()) {
    $todos[] = $row;
}

// Close the statement and connection
$stmt->close();
$mysqli->close();

// Send response
echo json_encode($todos);
?>
