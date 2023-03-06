<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (empty($email) || empty($password) || empty($role)) {
        $error = "Email, password, and role are required";
    } else {
        $dsn = 'mysql:host=localhost;dbname=ticket;charset=utf8mb4';
        $username = 'root';
        $db_password = '';

        try {
            $pdo = new PDO($dsn, $username, $db_password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email AND role = :role');
            $stmt->execute(['email' => $email, 'role' => $role]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            echo "User query returned: ";
            var_dump($user);
            var_dump($_SESSION);
            var_dump($_COOKIE);



    if ($user && password_verify($password, $user['password'])) {
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];

    // Set a cookie with the user's role
    setcookie('user_role', $user['role'], time() + 3600, '/');

    if ($user['role'] === 'client') {
        header('Location: create_ticket.php');
        exit();
    } elseif ($user['role'] === 'support') {
        header('Location: home.html');
        exit();
    }
} else {
    $error = "Incorrect email, password, or role";
}


        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

?>



<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body>
    <form action="" method="post">
        <div class="container mt-5">
            <h2 class="text-center">Login</h2>
            <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error ?>
            </div>
            <?php endif ?>
            <div class="form-group">
                <label for="email">Email address:</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
            </div>


             <div class="form-group">
                <label for="role">Role:</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="">Select a role</option>
                    <option value="Client">Client</option>
                    <option value="Support">Support</option>
                </select>
            </div>


            <button type="submit" class="btn btn-primary">Login</button>
       

        </div>
    </form>

    <div class="container mt-3">
        <p>Don't have an account? <a href="signup.php">Sign up</a> now!</p>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
