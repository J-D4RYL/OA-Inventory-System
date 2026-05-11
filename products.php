<?php
include 'config.php';
include 'auth.php';
checkStockManagerOrOwner();

$message = "";
$popupTitle = "";
$popupType = "success";

// MESSAGE FROM EDIT OR DELETE
if (isset($_GET['msg'])) {
    $message = $_GET['msg'];
    $popupTitle = $_GET['title'] ?? "Notice";
    $popupType = $_GET['type'] ?? "success";
}

// ADD PRODUCT
if (isset($_POST['add'])) {
    $name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $category = $_POST['category_id'];
    $size = mysqli_real_escape_string($conn, $_POST['size']);
    $color = mysqli_real_escape_string($conn, $_POST['color']);
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $low = $_POST['low_stock_limit'];

    $insert = mysqli_query($conn, "INSERT INTO products 
        (product_name, category_id, size, color, quantity, price, low_stock_limit)
        VALUES ('$name', '$category', '$size', '$color', '$quantity', '$price', '$low')");

    if ($insert) {
        $message = "Product '$name' added successfully!";
        $popupTitle = "Product Added";
        $popupType = "success";
    } else {
        $message = "Error adding product.";
        $popupTitle = "Error";
        $popupType = "error";
    }
}

// FETCH CATEGORIES
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY category_name ASC");

// SEARCH FUNCTION
$search = $_GET['search'] ?? '';

if (!empty($search)) {
    $search_safe = mysqli_real_escape_string($conn, $search);

    $products = mysqli_query($conn, "
        SELECT p.*, c.category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id 
        WHERE p.is_deleted = 0
        AND (
            p.product_name LIKE '%$search_safe%'
            OR c.category_name LIKE '%$search_safe%'
            OR p.size LIKE '%$search_safe%'
            OR p.color LIKE '%$search_safe%'
        )
        ORDER BY p.product_id DESC
    ");

    if (mysqli_num_rows($products) > 0) {
        $message = "Search results found for '$search_safe'.";
        $popupTitle = "Search Found";
        $popupType = "success";
    } else {
        $message = "No products found for '$search_safe'.";
        $popupTitle = "No Result";
        $popupType = "error";
    }

} else {
    $products = mysqli_query($conn, "
        SELECT p.*, c.category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id 
        WHERE p.is_deleted = 0
        ORDER BY p.product_id DESC
    ");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Products</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
<?php include 'nav.php'; ?>

<div class="container">
    <h1>Products</h1>

    <!-- POPUP MESSAGE -->
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

    <!-- ADD PRODUCT FORM -->
    <form method="POST">
        <input type="text" name="product_name" placeholder="Product Name" required>

        <select name="category_id" required>
            <option value="">Select Category</option>
            <?php while ($cat = mysqli_fetch_assoc($categories)) { ?>
                <option value="<?php echo $cat['category_id']; ?>">
                    <?php echo htmlspecialchars($cat['category_name']); ?>
                </option>
            <?php } ?>
        </select>

        <select name="size" required>
            <option value="S">S</option>
            <option value="M">M</option>
            <option value="L">L</option>
            <option value="XL">XL</option>
        </select>

        <input type="text" name="color" placeholder="Color" required>
        <input type="number" name="quantity" placeholder="Quantity" required>
        <input type="number" step="0.01" name="price" placeholder="Price" required>

        <label>Low Stock Alert</label>
        <input type="number" name="low_stock_limit" placeholder="Low Stock Limit" value="5" required>

        <button type="submit" name="add">Add Product</button>
    </form>

    <!-- SEARCH -->
    <form method="GET" action="products.php" class="search-form">
        <input type="text" name="search" placeholder="Search product..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn">Search</button>
    </form>

    <!-- PRODUCT TABLE -->
    <table>
        <tr>
            <th>Name</th><th>Category</th><th>Size</th><th>Color</th><th>Qty</th><th>Price</th><th>Action</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($products)) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
            <td><?php echo htmlspecialchars($row['category_name']); ?></td>
            <td><?php echo htmlspecialchars($row['size']); ?></td>
            <td><?php echo htmlspecialchars($row['color']); ?></td>
            <td><?php echo htmlspecialchars($row['quantity']); ?></td>
            <td>₱<?php echo number_format($row['price'], 2); ?></td>
            <td>
                <a href="edit_product.php?id=<?php echo $row['product_id']; ?>" class="btn">Edit</a>
                <a href="delete_product.php?id=<?php echo $row['product_id']; ?>" 
                   class="btn danger" 
                   onclick="return confirm('Delete this product?');">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>