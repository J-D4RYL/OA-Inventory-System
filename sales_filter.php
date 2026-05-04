<?php
include 'config.php';

$date = $_GET['date'] ?? '';

$query = "
SELECT s.sale_date, s.payment_method, s.total_amount,
p.product_name, p.size, p.color, sd.quantity_sold
FROM sales s
JOIN sale_details sd ON s.sale_id = sd.sale_id
JOIN products p ON sd.product_id = p.product_id
";

if ($date != "") {
    $query .= " WHERE DATE(s.sale_date) = '$date'";
}

$query .= " ORDER BY s.sale_date DESC";

$sales = mysqli_query($conn, $query);
?>