<?php
include 'config.php';
include 'auth.php';
checkOwner();

$date = $_GET['date'] ?? '';

if ($date != "") {
    $sales = mysqli_query($conn, "
        SELECT s.sale_id, s.sale_date, s.payment_method, s.total_amount,
        p.product_name, p.size, p.color, sd.quantity_sold
        FROM sales s
        JOIN sale_details sd ON s.sale_id = sd.sale_id
        JOIN products p ON sd.product_id = p.product_id
        WHERE DATE(s.sale_date) = '$date'
        ORDER BY s.sale_date DESC
    ");
} else {
    $sales = mysqli_query($conn, "
        SELECT s.sale_id, s.sale_date, s.payment_method, s.total_amount,
        p.product_name, p.size, p.color, sd.quantity_sold
        FROM sales s
        JOIN sale_details sd ON s.sale_id = sd.sale_id
        JOIN products p ON sd.product_id = p.product_id
        ORDER BY s.sale_date DESC
    ");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daily Sales Report</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'nav.php'; ?>

<div class="container">
    <h1>Daily Sales Report</h1>

    <!-- FILTER FORM -->
    <form method="GET" action="sales_report.php" class="search-form">
        <input type="date" name="date" value="<?php echo $_GET['date'] ?? ''; ?>">
        <button class="btn">Filter</button>
    </form>

    <table>
        <tr>
            <th>Date</th>
            <th>Product</th>
            <th>Size</th>
            <th>Color</th>
            <th>Qty</th>
            <th>Payment</th>
            <th>Total</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($sales)) { ?>
        <tr>
            <td><?php echo $row['sale_date']; ?></td>
            <td><?php echo $row['product_name']; ?></td>
            <td><?php echo $row['size']; ?></td>
            <td><?php echo $row['color']; ?></td>
            <td><?php echo $row['quantity_sold']; ?></td>
            <td><?php echo $row['payment_method']; ?></td>
            <td>₱<?php echo number_format($row['total_amount'], 2); ?></td>
        </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>