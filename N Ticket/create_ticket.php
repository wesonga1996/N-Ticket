<?php

// Check if the user is logged in and has a "user_role" cookie set to "client"
/*if (!isset($_SESSION['email']) || !isset($_COOKIE['user_role']) || $_COOKIE['user_role'] !== 'client') {
    // Redirect to a login page or display an error message
    header('Location: index.php');
    exit();
}*/

// Connect to the database 
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ticket";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set error reporting to display errors only during development
error_reporting(E_ALL);
ini_set('display_errors', '1');

$message = "";

$stmt= null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = filter_var($_POST['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $type = filter_var($_POST['type'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $subject = filter_var($_POST['subject'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $message = filter_var($_POST['message'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Validate file upload
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
        // Check if the file is a valid type and size
        $allowedTypes = array('jpg', 'jpeg', 'png', 'gif');
        $maxSize = 1048576; // 1 MB
        $fileType = strtolower(pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION));
        $fileSize = $_FILES['attachment']['size'];
        if (in_array($fileType, $allowedTypes) && $fileSize <= $maxSize) {
            // Move the file to the uploads directory
            $targetDir = "uploads/";
            $targetFile = $targetDir . basename($_FILES['attachment']['name']);
            if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetFile)) {
                $attachment = $targetFile;
            } else {
                $attachment = null;
                $message = "Error uploading file";
            }
        } else {
            $attachment = null;
            $message = "Invalid file type or size";
        }
    } else {
        $attachment = null;
    }

    // Insert form data into the database (using prepared statements)
    $stmt = $conn->prepare("INSERT INTO tickets (name, email, type, subject, message, attachment) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssb", $name, $email, $type, $subject, $message, $attachment);
    if ($stmt->execute()) {

        // Assuming the booking was successful and a variable called $ticketNumber was set with the ticket number
        $ticketNumber = rand(100000000, 999999999);

        // Generate the message response
        $message = "Your ticket with number $ticketNumber has been booked successfully. ";

    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Close the database connection
$conn->close();

?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticketing Form</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body>
    <form action="connect.php" method="post" enctype="multipart/form-data">

        <div class="container mt-5">
            <h2 class="text-center">Create Ticket</h2>
  
            <div class="form-group">
                <label for="name">Company Name:</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" required>
            </div>
            <div class="form-group">
                <label for="email">Email address:</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
            </div>
            <div class="form-group">
                <label for="type">Type of Issue:</label>
                <input type="text" class="form-control" id="type" name="type" placeholder="E.g. Technical, Integration, etc." required>
            </div>
            <div class="form-group">
                <label for="subject">Subject:</label>
                <input type="text" class="form-control" id="subject" name="subject" placeholder="Enter a brief subject line" required>
            </div>
            <div class="form-group">
                <label for="message">Message:</label>
                <textarea class="form-control" id="message" name="message" placeholder="Enter your message here" required></textarea>
            </div>
            <div class="form-group">
                <label for="attachment">Attach File or Picture:</label>
                <input type="file" class="form-control-file" id="attachment" name="attachment">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>
     
   

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="dashboard.js"></script>
</body>
</html>
