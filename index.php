<?php
include 'config.php';

$error = "";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = trim($_POST['password']);

    $query = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'owner') {
                header("Location: /inventory_system/dashboard.php");
            } elseif ($user['role'] == 'cashier') {
                header("Location: /inventory_system/cashier.php");
            } elseif ($user['role'] == 'stock_manager') {
                header("Location: /inventory_system/stock_dashboard.php");
            }
            exit();
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>OA Clothing Inventory Login</title>
    <link rel="stylesheet" href="/inventory_system/style.css">
</head>
<body>
<div class="login-box">
    <h2>OA Clothing Inventory</h2>

    <?php if ($error != "") { ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php } ?>

    <form method="POST" action="/inventory_system/index.php">
        <label>Username</label>
        <input type="text" name="username" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit" name="login">Login</button>
    </form>
</div>
</body>
</html>