<?php
session_start();

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

// Check if the login form data has been submitted
if (!isset($_POST['username'], $_POST['password'])) {
    exit('Please fill both the username and password fields!');
}

// Prepare SQL statement to prevent SQL injection
if ($stmt = $con->prepare('SELECT id, password FROM accounts WHERE username = ?')) {
    // Bind parameters
    $stmt->bind_param('s', $_POST['username']);
    $stmt->execute();
    // Store the result to check if the account exists
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Bind the result to variables
        $stmt->bind_result($id, $password);
        $stmt->fetch();

        // Verify the password
        if (password_verify($_POST['password'], $password)) {
            // Password is correct, create sessions
            session_regenerate_id();
            $_SESSION['loggedin'] = TRUE;
            $_SESSION['name'] = $_POST['username'];
            $_SESSION['id'] = $id;

            header('Location: index.html');
            exit;    
            } else {
            // Incorrect password
            header('Location: login.html');

        }
    } else {
        // Incorrect username
        header('Location: login.html');
    }

    // Close the statement
    $stmt->close();
} else {
    // SQL statement preparation failed
    echo 'Could not prepare statement!';
}

// Close the database connection
$con->close();
?>
