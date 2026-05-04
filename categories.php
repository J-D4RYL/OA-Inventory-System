<?php
include 'config.php';
include 'auth.php';
checkOwner();

if (isset($_POST['add'])) {
    $name = mysqli_real_escape_string($conn, $_POST['category_name']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    mysqli_query($conn, "INSERT INTO categories (category_name, description) VALUES ('$name', '$desc')");
}

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY category_id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Categories</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'nav.php'; ?>
<div class="container">
    <h1>Categories</h1>
    <form method="POST">
        <input type="text" name="category_name" placeholder="Category Name" required>
        <input type="text" name="description" placeholder="Description">
        <button type="submit" name="add">Add Category</button>
    </form>

    <table>
        <tr><th>Category</th><th>Description</th><th>Status</th></tr>
        <?php while ($row = mysqli_fetch_assoc($categories)) { ?>
        <tr>
            <td><?php echo $row['category_name']; ?></td>
            <td><?php echo $row['description']; ?></td>
            <td><?php echo $row['status']; ?></td>
        </tr>
        <?php } ?>
    </table>
</div>
</body>
</html>
