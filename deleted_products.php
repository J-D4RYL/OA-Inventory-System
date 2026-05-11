<?php
include 'config.php';
include 'auth.php';
checkStockManagerOrOwner();

$message = "";
$popupTitle = "";
$popupType = "success";

/* RESTORE PRODUCT */
if (isset($_GET['restore_id'])) {
    $id = $_GET['restore_id'];

    $getProduct = mysqli_query($conn, "SELECT product_name FROM products WHERE product_id = '$id'");
    $product = mysqli_fetch_assoc($getProduct);
    $productName = $product['product_name'] ?? "Product";

    $restore = mysqli_query($conn, "UPDATE products SET is_deleted = 0 WHERE product_id = '$id'");

    if ($restore) {
        $message = "Product '$productName' restored successfully.";
        $popupTitle = "Product Restored";
        $popupType = "success";
    } else {
        $message = "Error restoring product.";
        $popupTitle = "Error";
        $popupType = "error";
    }
}

/* PERMANENT DELETE PRODUCT */
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];

    $getProduct = mysqli_query($conn, "SELECT product_name FROM products WHERE product_id = '$id'");
    $product = mysqli_fetch_assoc($getProduct);
    $productName = $product['product_name'] ?? "Product";

    $delete = mysqli_query($conn, "DELETE FROM products WHERE product_id = '$id'");

    if ($delete) {
        $message = "Product '$productName' permanently deleted.";
        $popupTitle = "Deleted Permanently";
        $popupType = "success";
    } else {
        $message = "Cannot permanently delete product. It may already be used in sales records.";
        $popupTitle = "Error";
        $popupType = "error";
    }
}

/* FETCH DELETED PRODUCTS */
$deletedProducts = mysqli_query($conn, "
    SELECT p.*, c.category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.category_id 
    WHERE p.is_deleted = 1
    ORDER BY p.product_id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Deleted Products</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'nav.php'; ?>

<div class="container">
    <h1>Deleted Products</h1>

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

    <table>
        <tr>
            <th>Name</th>
            <th>Category</th>
            <th>Size</th>
            <th>Color</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Action</th>
        </tr>

        <?php if (mysqli_num_rows($deletedProducts) > 0) { ?>
            <?php while ($row = mysqli_fetch_assoc($deletedProducts)) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['size']); ?></td>
                    <td><?php echo htmlspecialchars($row['color']); ?></td>
                    <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                    <td>₱<?php echo number_format($row['price'], 2); ?></td>
                    <td>
                        <a href="deleted_products.php?restore_id=<?php echo $row['product_id']; ?>" 
                           class="btn"
                           onclick="return confirm('Restore this product back to stock?');">
                            Restore
                        </a>

                        <a href="deleted_products.php?delete_id=<?php echo $row['product_id']; ?>" 
                           class="btn danger"
                           onclick="return confirm('Permanently delete this product? This cannot be undone.');">
                            Delete Permanently
                        </a>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="7" style="text-align:center;">No deleted products.</td>
            </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>