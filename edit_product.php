<?php
include 'config.php';
include 'auth.php';
checkStockManagerOrOwner();

$id = $_GET['id'];

// FETCH PRODUCT
$product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE product_id='$id'"));

if (!$product) {
    header("Location: products.php?msg=" . urlencode("Product not found.") . "&title=Error&type=error");
    exit;
}

if (isset($_POST['update'])) {
    $name = mysqli_real_escape_string($conn, trim($_POST['product_name']));
    $size = mysqli_real_escape_string($conn, trim($_POST['size']));
    $color = mysqli_real_escape_string($conn, trim($_POST['color']));
    $qty = (int) $_POST['quantity'];
    $price = (float) $_POST['price'];

    if (empty($name) || empty($size) || empty($color)) {
        header("Location: products.php?msg=" . urlencode("Please complete all fields.") . "&title=Invalid Input&type=error");
        exit;
    }

    if ($qty < 0) {
        header("Location: products.php?msg=" . urlencode("Quantity cannot be negative.") . "&title=Invalid Quantity&type=error");
        exit;
    }

    if ($price <= 0) {
        header("Location: products.php?msg=" . urlencode("Price must be greater than zero.") . "&title=Invalid Price&type=error");
        exit;
    }

    $update = mysqli_query($conn, "
        UPDATE products 
        SET product_name='$name', size='$size', color='$color', quantity='$qty', price='$price'
        WHERE product_id='$id'
    ");

    if ($update) {
        header("Location: products.php?msg=" . urlencode("Product '$name' updated successfully!") . "&title=" . urlencode("Product Updated") . "&type=success");
        exit;
    } else {
        header("Location: products.php?msg=" . urlencode("Error updating product.") . "&title=Error&type=error");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'nav.php'; ?>

<div class="container">
    <h2>Edit Product</h2>

    <form method="POST">

        <label>Product Name</label>
        <input type="text" name="product_name" 
               value="<?php echo htmlspecialchars($product['product_name']); ?>" required>

        <label>Size</label>
        <select name="size" required>
            <option value="S" <?php if ($product['size'] == 'S') echo 'selected'; ?>>S</option>
            <option value="M" <?php if ($product['size'] == 'M') echo 'selected'; ?>>M</option>
            <option value="L" <?php if ($product['size'] == 'L') echo 'selected'; ?>>L</option>
            <option value="XL" <?php if ($product['size'] == 'XL') echo 'selected'; ?>>XL</option>
        </select>

        <label>Color</label>
        <input type="text" name="color" 
               value="<?php echo htmlspecialchars($product['color']); ?>" required>

        <label>Quantity</label>
        <input type="number" name="quantity" min="0"
               value="<?php echo htmlspecialchars($product['quantity']); ?>" required>

        <label>Price</label>
        <input type="number" step="0.01" min="0.01" name="price" 
               value="<?php echo htmlspecialchars($product['price']); ?>" required>

        <button name="update">Update Product</button>
    </form>

    <br>
    <a href="products.php" class="btn">Back to Products</a>
</div>

</body>
</html>