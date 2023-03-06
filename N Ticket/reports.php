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

// Construct an SQL query to retrieve the number of tickets created by each user and their corresponding message and reply status
$sql = "SELECT tickets.name, 
       tickets.email,
       COUNT(tickets.id) AS total_tickets,
       SUM(CASE WHEN tickets.status = 'closed' THEN 1 ELSE 0 END) AS total_closed_tickets,
       SUM(CASE WHEN replies.id IS NULL THEN 0 ELSE 1 END) AS total_replied_tickets,
       GROUP_CONCAT(tickets.subject SEPARATOR '; ') AS subjects,
       GROUP_CONCAT(tickets.status SEPARATOR '; ') AS statuses,
       GROUP_CONCAT(replies.message SEPARATOR '; ') AS messages,
       GROUP_CONCAT(CASE WHEN replies.id IS NULL THEN 'not replied' ELSE 'replied' END SEPARATOR '; ') AS reply_statuses,
       GROUP_CONCAT(tickets.created_at SEPARATOR '; ') AS created_times
FROM tickets
LEFT JOIN replies ON tickets.id = replies.ticket_id
GROUP BY tickets.name";

// Execute the SQL query and store the result in the $result variable
$result = $conn->query($sql);

// Check if the query returned any rows
if ($result->num_rows > 0) {
   // Output the HTML table with Bootstrap headers
   echo '<!DOCTYPE html>
        <html>
            <head>
                <meta charset="UTF-8">
                <title>Ticket Reports</title>
                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
            </head>
            <body>
                <div class="container">
                    <h1>Ticket Reports</h1>
                    <table class="table">
                        <thead>
                             <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Subjects</th>
            <th>Statuses</th>
            <th>Messages</th>
            <th>Reply Statuses</th>
            <th>Total Tickets</th>
            <th>Total Closed Tickets</th>
            <th>Total Replied Tickets</th>
            <th>Created Times</th>
        </tr>
                        </thead>
                        <tbody>';

  while($row = $result->fetch_assoc()) {
    // Print the ticket data
    echo "<tr>";
    echo "<td>" . $row["name"] . "</td>";
    echo "<td>" . $row["email"] . "</td>";
    echo "<td>" . $row["subjects"] . "</td>";
    echo "<td>" . $row["statuses"] . "</td>";
    echo "<td>" . $row["messages"] . "</td>";
    echo "<td>" . $row["reply_statuses"] . "</td>";
    echo "<td>" . $row["total_tickets"] . "</td>";
    echo "<td>" . $row["total_closed_tickets"] . "</td>";
    echo "<td>" . $row["total_replied_tickets"] . "</td>";
    echo "<td>" . $row["created_times"] . "</td>";
    echo "</tr>";
}

    // Close the HTML table and container
    echo '</tbody></table></div>
          <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
          <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
          <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
        </body>
    </html>';
} else {
    echo "No tickets found";
}

// Close the database connection
$conn->close();

?>
