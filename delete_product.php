<?php
include 'config.php';
include 'auth.php';
checkOwner();

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $getProduct = mysqli_query($conn, "SELECT product_name FROM products WHERE product_id = '$id'");
    $product = mysqli_fetch_assoc($getProduct);
    $productName = $product['product_name'] ?? "Product";

    $delete = mysqli_query($conn, "DELETE FROM products WHERE product_id = '$id'");

    if ($delete) {
        header("Location: products.php?msg=" . urlencode("Product '$productName' deleted successfully!") . "&title=" . urlencode("Product Deleted") . "&type=success");
        exit;
    } else {
        header("Location: products.php?msg=" . urlencode("Error deleting product.") . "&title=Error&type=error");
        exit;
    }
}
?>