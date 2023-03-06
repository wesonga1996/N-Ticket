
<?php

    // Connect to the database (replace the placeholders with your actual credentials)
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
        $message = "Ticket submitted successfully";

// Assuming the booking was successful and a variable called $ticketNumber was set with the ticket number
$ticketNumber =rand(100000000, 999999999);

// Generate the message response
$message = "Your ticket with number $ticketNumber has been booked successfully. ";

// Output the message response
echo $message;

    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Close the database connection
$conn->close();

?>




