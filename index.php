<?php
include 'config.php';

$error = "";

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $query = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        // Works for plain password 12345
        if ($password == $user['password']) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

           if ($user['role'] == 'owner') {
                 header("Location: dashboard.php");
            } elseif ($user['role'] == 'cashier') {
                header("Location: cashier.php");
            } elseif ($user['role'] == 'stock_manager') {
                 header("Location: stock_dashboard.php");
            } else {
                  header("Location: index.php");
                   }
            exit();
            
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "User not found.";
    }
}
 //user:owner pass: 12345
   //cashier pass:12345
?>
<!DOCTYPE html>
<html>
<head>
    <title>OA Clothing Inventory Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="login-box">
    <h2>OA Clothing Inventory</h2>
    <?php if ($error != "") echo "<p class='error'>$error</p>"; ?>
    <form method="POST">
        <label>Username</label>
        <input type="text" name="username" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit" name="login">Login</button>
    </form>
  
</div>
</body>
</html>
