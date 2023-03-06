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
    // Update the ticket status to "replied"
    $sql = "UPDATE tickets SET status='replied' WHERE id=$ticket_id";
    $conn->query($sql);
    // Insert the reply message into the "replies" table
    $sql = "INSERT INTO replies (ticket_id, message) VALUES ($ticket_id, '$reply_message')";
    $conn->query($sql);
}

// Construct an SQL query to retrieve all rows from the "tickets" table with a non-null "created_at" column
$sql = "SELECT * FROM tickets WHERE created_at IS NOT NULL";

// Execute the SQL query and store the result in the $result variable
$result = $conn->query($sql);

// Check if the query returned any rows
if ($result->num_rows > 0) {
   // Output the HTML table with Bootstrap headers
echo '<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>My Tickets</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    </head>

    <body>
        <div class="container">
            <h1>My Tickets</h1>
            <table class="table">
                <thead>
                    <tr>
                        <th>Ticket #</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Type</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Attachment</th>
                        <th>Created at</th>
                        <th>Response</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>';
while($row = $result->fetch_assoc()) {
    // Print the ticket data
    echo "<tr>";
    echo "<td>" . $row["id"] . "</td>";
    echo "<td>" . $row["name"] . "</td>";
    echo "<td>" . $row["email"] . "</td>";
    echo "<td>" . $row["type"] . "</td>";
    echo "<td>" . $row["subject"] . "</td>";
    echo "<td>" . $row["message"] . "</td>";
    echo "<td>" . $row["attachment"] . "</td>";
    echo "<td>" . $row["created_at"] . "</td>";
    echo "<td><form method='POST' action='reply.php'><input type='hidden' name='ticket_id' value='" . $row["id"] . "'><input type='text' name='reply_message' class='form-control'></td>";
    echo "<td><button type='submit' class='btn btn-primary'>Reply</button></form></td>";

    echo "</tr>";
}
// Close the HTML table and container
echo '</tbody></table></div>
    </body>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</html>';
} else {
    echo "No tickets found";
}

// Close the database connection
$conn->close();



?>
