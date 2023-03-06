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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the ticket ID from the form data
    $ticket_id = $_POST["ticket_id"];
    // Get the reply message from the form data
    $reply_message = $_POST["reply_message"];
    
    // Retrieve the customer email address from the database
    $sql = "SELECT email, name FROM tickets WHERE id=$ticket_id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $customer_email = $row["email"];
        $customer_name = $row["name"];
    }

    // Check if the direct_mail flag is set to "yes"
    if (isset($_POST["direct_mail"]) && $_POST["direct_mail"] == "yes") {
        // Generate an email message and send it to the customer's email address
        $to = $customer_email;
        $subject = "RE: Ticket #$ticket_id";
        $message = "Dear $customer_name,\n\n" .
                   "Thank you for contacting us regarding Ticket #$ticket_id. " .
                   "Please find our response below:\n\n" .
                   "$reply_message\n\n" .
                   "If you have any further questions or concerns, please don't hesitate to contact us.\n\n" .
                   "Best regards,\n" .
                   "Your support team";
        $headers = "From: yourname@example.com\r\n" .
                   "Reply-To: yourname@example.com\r\n" .
                   "X-Mailer: PHP/" . phpversion();
        mail($to, $subject, $message, $headers);
    } else {
        // Update the ticket status to "replied"
        $sql = "UPDATE tickets SET status='replied' WHERE id=$ticket_id";
        $conn->query($sql);
        // Insert the reply message into the "replies" table
        $sql = "INSERT INTO replies (ticket_id, message) VALUES ($ticket_id, '$reply_message')";
        $conn->query($sql);
    }
}


// Close the database connection
$conn->close();

// Redirect the user back to the ticket table
header("Location: retreive.php");
exit();
