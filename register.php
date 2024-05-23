<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Database connection details
    $DATABASE_HOST = 'localhost';
    $DATABASE_USER = 'root';
    $DATABASE_PASS = 'root';
    $DATABASE_NAME = 'eLearn';
    $DATABASE_PORT = 8888; // Specify the port number

    // Connect to MySQL database
    $con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME, $DATABASE_PORT);

    // Check for connection errors
    if (mysqli_connect_errno()) {
        exit('Failed to connect to MySQL: ' . mysqli_connect_error());
    }

    // Check if the registration form data has been submitted
    if (!isset($_POST['username'], $_POST['password'], $_POST['email'])) {
        exit('Please fill both the username and password fields!');
    }

    // Validate the username and password (e.g., ensure they are not empty)
    if (empty($_POST['username']) || empty($_POST['password']) || empty( $_POST['email'])) {
        exit('Please fill both the username and password fields!');
    }

    // Prepare SQL statement to prevent SQL injection
    if ($stmt = $con->prepare('INSERT INTO accounts (username, password, email) VALUES (?, ?, ?)')) {
        // Hash the password
        $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Bind parameters and execute the statement
        $stmt->bind_param('sss', $_POST['username'], $hashed_password,  $_POST['email']);
        if ($stmt->execute()) {
            header('Location: login.html');
        } else {
            header('Location: register.html');
        }

        // Close the statement
        $stmt->close();
    } else {
        // SQL statement preparation failed
        header('Location: register.html');
    }

    // Close the database connection
    $con->close();
}
?>
