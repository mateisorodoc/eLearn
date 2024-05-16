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

// Prepare and bind the SQL statement to update status and retrieve creation time
$sql = "UPDATE todo SET status = ?, completion_time = NOW() WHERE content = ?";
$stmt = $mysqli->prepare($sql);

// Check for errors in preparing the statement
if (!$stmt) {
    die('Error in preparing statement: ' . $mysqli->error);
}

// Get content and status from POST data
$content = $_POST["content"] ?? '';
$status = $_POST["status"] ?? 0; // Default status to 0 if not provided

// Bind parameters to the prepared statement
$stmt->bind_param("is", $status, $content);

// Execute the statement
if (!$stmt->execute()) {
    die('Error executing statement: ' . $stmt->error);
}

// Close the statement
$stmt->close();

// If the status is set to 1 (completed), calculate and append the time taken
if ($status == 1) {
    // Retrieve the creation time from the database
    $sql = "SELECT creation_time FROM todo WHERE content = ?";
    $stmt = $mysqli->prepare($sql);

    // Check for errors in preparing the statement
    if (!$stmt) {
        die('Error in preparing statement: ' . $mysqli->error);
    }

    // Bind parameters to the prepared statement
    $stmt->bind_param("s", $content);

    // Execute the statement
    if (!$stmt->execute()) {
        die('Error executing statement: ' . $stmt->error);
    }

    // Bind result variable
    $stmt->bind_result($creation_time);

    // Fetch the result
    $stmt->fetch();

    // Calculate the time taken
    $completion_time = date("Y-m-d H:i:s");
    $time_taken = strtotime($completion_time) - strtotime($creation_time);

    // Close the statement
    $stmt->close();

    // Update the task content with the time taken
    $new_content = $content . " (Completed in " . gmdate("i:s", $time_taken) . ")";
    $sql = "UPDATE todo SET content = ? WHERE content = ?";
    $stmt = $mysqli->prepare($sql);

    // Check for errors in preparing the statement
    if (!$stmt) {
        die('Error in preparing statement: ' . $mysqli->error);
    }

    // Bind parameters to the prepared statement
    $stmt->bind_param("ss", $new_content, $content);

    // Execute the statement
    if (!$stmt->execute()) {
        die('Error executing statement: ' . $stmt->error);
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$mysqli->close();

// Return JSON response
$response = array("success" => true);
echo json_encode($response);
?>
