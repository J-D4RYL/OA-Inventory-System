<?php
include 'config.php';
include 'auth.php';
checkOwner();

$totalProducts = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM products"))['total'];
$totalSales = mysqli_fetch_assoc(mysqli_query($conn, "SELECT IFNULL(SUM(total_amount),0) AS total FROM sales"))['total'];
$lowStock = mysqli_query($conn, "SELECT * FROM products WHERE quantity <= low_stock_limit ORDER BY quantity ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Owner Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'nav.php'; ?>
<div class="container">
    <h1>Owner Dashboard</h1>
    <p><b>Total Products:</b> <?php echo $totalProducts; ?></p>
    <p><b>Total Sales:</b> ₱<?php echo number_format($totalSales, 2); ?></p>

    <h2>Low Stock Alert</h2>
    <table>
        <tr>
            <th>Product</th>
            <th>Size</th>
            <th>Color</th>
            <th>Quantity</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($lowStock)) { ?>
        <tr>
            <td><?php echo $row['product_name']; ?></td>
            <td><?php echo $row['size']; ?></td>
            <td><?php echo $row['color']; ?></td>
            <td class="warning"><?php echo $row['quantity']; ?></td>
        </tr>
        <?php } ?>
    </table>
</div>
</body>
</html>
