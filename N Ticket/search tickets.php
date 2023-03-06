<?php




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

// Initialize an empty array to hold retrieved tickets
$tickets = array();

// If the request method is POST, retrieve tickets created by the user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'])) {
    // Sanitize the user input for name to prevent SQL injection
    $name = filter_var($_POST['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
}


    // Prepare and execute a SELECT query to retrieve tickets for the specified name
    $stmt = $conn->prepare("SELECT * FROM tickets WHERE name = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();

    // Get the result set and store each row in the $tickets array
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $tickets[] = $row;
    }

    // Close the prepared statement
    $stmt->close();

// Close the database connection
$conn->close();

?>


<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Search</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.1/dist/tailwind.min.css">
  </head>

  <?php include('home.html'); ?>

  <body class="bg-gray-100">
    <div class="container mx-auto my-8 px-4 max-w-lg">
      <h1 class="text-3xl text-center mb-4">Search for Tickets</h1>

      <form action="search tickets.php" method="post">
        <div class="flex flex-col gap-4">
          <label for="name" class="text-lg">Enter name:</label>
          <input type="name" id="name" name="name" class="rounded-md border-gray-400 px-3 py-2" placeholder="Company name" required>
          <button type="submit" class="bg-blue-500 text-white rounded-md py-2 px-4 hover:bg-blue-600">Search</button>
        </div>
      </form>

      <?php if (!empty($tickets)) : ?>
       <h2 class="text-2xl text-center mt-8 mb-4">Your Tickets</h2>
<table class="table-auto border border-gray-400 w-full">
  <thead>
    <tr class="bg-gray-200">
      <th class="px-4 py-2">#</th>
      <th class="px-4 py-2">Name</th>
      <th class="px-4 py-2">Email</th>
      <th class="px-4 py-2">Type</th>
      <th class="px-4 py-2">Subject</th>
      <th class="px-4 py-2">Message</th>
      <th class="px-4 py-2">Time Created</th>
      <th class="px-4 py-2">Status</th>
      <th class="px-4 py-2">Attachment</th>
    </tr>
  </thead>
  <tbody>
    
            <?php foreach ($tickets as $ticket) : ?>
              <tr class="border border-gray-400">
                <td class="px-4 py-2"><?php echo $ticket['id']; ?></td>
                <td class="px-4 py-2"><?php echo $ticket['name']; ?></td>
                <td class="px-4 py-2"><?php echo $ticket['email']; ?></td>
                <td class="px-4 py-2"><?php echo $ticket['type']; ?></td>
                <td class="px-4 py-2"><?php echo $ticket['subject']; ?></td>
                <td class="px-4 py-2"><?php echo $ticket['message']; ?></td>
                <td class="px-4 py-2"><?php echo $ticket['created_at']; ?></td>
                <td class="px-4 py-2"><?php echo $ticket['status']; ?></td>
                <td class="px-4 py-2">
                  <?php if (!empty($ticket['attachment'])) : ?>
                    <a href="<?php echo $ticket['attachment']; ?>" target="_blank" class="text-blue-500 hover:text-blue-700">Download</a>
                  <?php else : ?>
                    <span class="text-gray-400">None</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </body>
</html>


