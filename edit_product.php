<?php
include 'config.php';

$id = $_GET['id'];

// FETCH PRODUCT
$product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE product_id='$id'"));

if (isset($_POST['update'])) {
    $name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $size = mysqli_real_escape_string($conn, $_POST['size']);
    $color = mysqli_real_escape_string($conn, $_POST['color']);
    $qty = $_POST['quantity'];
    $price = $_POST['price'];

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

<div class="container">
    <h2>Edit Product</h2>

    <form method="POST">

        <label>Product Name</label>
        <input type="text" name="product_name" 
               value="<?php echo htmlspecialchars($product['product_name']); ?>" required>

        <label>Size</label>
                <select name="size" required>
            <option value="S">S</option>
            <option value="M">M</option>
            <option value="L">L</option>
            <option value="XL">XL</option>
        </select>

        <label>Color</label>
        <input type="text" name="color" 
               value="<?php echo htmlspecialchars($product['color']); ?>" required>

        <label>Quantity</label>
        <input type="number" name="quantity" 
               value="<?php echo $product['quantity']; ?>" required>

        <label>Price</label>
        <input type="number" step="0.01" name="price" 
               value="<?php echo $product['price']; ?>" required>

        <button name="update">Update Product</button>
    </form>
</div>

</body>
</html>