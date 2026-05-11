<?php
include 'config.php';
include 'auth.php';
checkOwner();

$message = "";
$popupTitle = "";
$popupType = "success";

if (isset($_POST['add'])) {
    $name = mysqli_real_escape_string($conn, trim($_POST['category_name']));
    $desc = mysqli_real_escape_string($conn, trim($_POST['description']));

    if (empty($name)) {
        $message = "Category name is required.";
        $popupTitle = "Invalid Input";
        $popupType = "error";
    } else {
        $insert = mysqli_query($conn, "
            INSERT INTO categories (category_name, description) 
            VALUES ('$name', '$desc')
        ");

        if ($insert) {
            $message = "Category '$name' added successfully!";
            $popupTitle = "Category Added";
            $popupType = "success";
        } else {
            $message = "Error adding category.";
            $popupTitle = "Error";
            $popupType = "error";
        }
    }
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

    <?php if (!empty($message)) { ?>
        <div class="popup-overlay" id="popupAlert">
            <div class="popup-box <?php echo htmlspecialchars($popupType); ?>">
                <div class="popup-icon">
                    <?php echo ($popupType == "error") ? "!" : "✓"; ?>
                </div>

                <h2><?php echo htmlspecialchars($popupTitle); ?></h2>
                <p><?php echo htmlspecialchars($message); ?></p>

                <button onclick="document.getElementById('popupAlert').style.display='none'">OK</button>
            </div>
        </div>
    <?php } ?>

    <form method="POST">
        <input type="text" name="category_name" placeholder="Category Name" required>
        <input type="text" name="description" placeholder="Description">
        <button type="submit" name="add">Add Category</button>
    </form>

    <table>
        <tr>
            <th>Category</th>
            <th>Description</th>
            <th>Status</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($categories)) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['category_name']); ?></td>
            <td><?php echo htmlspecialchars($row['description']); ?></td>
            <td><?php echo htmlspecialchars($row['status']); ?></td>
        </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>