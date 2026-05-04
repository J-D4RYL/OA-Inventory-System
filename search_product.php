<?php
include 'config.php';

$search = $_GET['search'] ?? '';

$query = "
SELECT p.*, c.category_name 
FROM products p 
LEFT JOIN categories c ON p.category_id = c.category_id
";

if ($search != "") {
    $query .= " WHERE p.product_name LIKE '%$search%' 
                OR p.color LIKE '%$search%' 
                OR p.size LIKE '%$search%'";
}

$query .= " ORDER BY p.product_id DESC";

$products = mysqli_query($conn, $query);
?>