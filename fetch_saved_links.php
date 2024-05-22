<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['loggedin']) || !isset($_SESSION['name'])) {
    echo json_encode([]);
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
    echo json_encode([]);
    exit;
}

$username = $_SESSION['name'];

// Fetch saved links
$sql = "SELECT title, url FROM saved_links WHERE username = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$saved_links = [];
while ($row = $result->fetch_assoc()) {
    $saved_links[] = $row;
}

// Close the connection
$stmt->close();
$mysqli->close();

echo json_encode($saved_links);
?>
