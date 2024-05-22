<?php
session_start();

header('Content-Type: application/json');

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
$data = json_decode(file_get_contents('php://input'), true);
$title = $data["title"] ?? '';
$url = $data["url"] ?? '';
$username = $_SESSION['name'];

// Check if the link already exists
$sql_check = "SELECT * FROM saved_links WHERE title = ? AND url = ? AND username = ?";
$stmt_check = $mysqli->prepare($sql_check);
$stmt_check->bind_param("sss", $title, $url, $username);
$stmt_check->execute();
$stmt_check->store_result();
if ($stmt_check->num_rows > 0) {
    // Link already exists, delete it
    $sql_delete = "DELETE FROM saved_links WHERE title = ? AND url = ? AND username = ?";
    $stmt_delete = $mysqli->prepare($sql_delete);
    $stmt_delete->bind_param("sss", $title, $url, $username);
    $stmt_delete->execute();
    $response = array("success" => true);
} else {
    // Link doesn't exist, insert it
    $sql_insert = "INSERT INTO saved_links (username, title, url) VALUES (?, ?, ?)";
    $stmt_insert = $mysqli->prepare($sql_insert);
    $stmt_insert->bind_param("sss", $username, $title, $url);
    $stmt_insert->execute();
    $response = array("success" => true);
}

// Close the statement and connection
$stmt_check->close();
$mysqli->close();

// Send response
echo json_encode($response);
?>
