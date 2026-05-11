<?php
include 'config.php';
include 'auth.php';
checkOwner();

$filter = $_GET['filter'] ?? 'all';

$where = "";

if ($filter == "daily") {
    $where = "WHERE DATE(s.sale_date) = CURDATE()";
} elseif ($filter == "weekly") {
    $where = "WHERE YEARWEEK(s.sale_date, 1) = YEARWEEK(CURDATE(), 1)";
} elseif ($filter == "monthly") {
    $where = "WHERE MONTH(s.sale_date) = MONTH(CURDATE()) 
              AND YEAR(s.sale_date) = YEAR(CURDATE())";
} elseif ($filter == "yearly") {
    $where = "WHERE YEAR(s.sale_date) = YEAR(CURDATE())";
}

$sales = mysqli_query($conn, "
    SELECT s.sale_id, s.sale_date, s.payment_method, s.total_amount,
           p.product_name, p.size, p.color, sd.quantity_sold
    FROM sales s
    JOIN sale_details sd ON s.sale_id = sd.sale_id
    JOIN products p ON sd.product_id = p.product_id
    $where
    ORDER BY s.sale_date DESC
");

$totalSales = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT IFNULL(SUM(s.total_amount), 0) AS total
    FROM sales s
    $where
"))['total'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sales Report</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'nav.php'; ?>

<div class="container">
    <h1>Sales Report</h1>

    <form method="GET" action="sales_report.php" class="search-form">
        <label>Filter Sales</label>
        <select name="filter">
            <option value="all" <?php if ($filter == "all") echo "selected"; ?>>Entire Sales</option>
            <option value="daily" <?php if ($filter == "daily") echo "selected"; ?>>Today</option>
            <option value="weekly" <?php if ($filter == "weekly") echo "selected"; ?>>Weekly Sales</option>
            <option value="monthly" <?php if ($filter == "monthly") echo "selected"; ?>>Monthly Sales</option>
            <option value="yearly" <?php if ($filter == "yearly") echo "selected"; ?>>Yearly Sales</option>
        </select>

        <button class="btn">Apply Filter</button>
    </form>

    <h2>
        Total Sales: ₱<?php echo number_format($totalSales, 2); ?>
    </h2>

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

        <?php if (mysqli_num_rows($sales) > 0) { ?>
            <?php while ($row = mysqli_fetch_assoc($sales)) { ?>
                <tr>
                    <td><?php echo $row['sale_date']; ?></td>
                    <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['size']); ?></td>
                    <td><?php echo htmlspecialchars($row['color']); ?></td>
                    <td><?php echo htmlspecialchars($row['quantity_sold']); ?></td>
                    <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                    <td>₱<?php echo number_format($row['total_amount'], 2); ?></td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="7" style="text-align:center;">No sales found.</td>
            </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>