<?php
include 'config.php';
include 'auth.php';
checkStockManagerOrOwner();

$totalProducts = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM products"))['total'];

$lowStockCount = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total 
    FROM products 
    WHERE quantity <= low_stock_limit
"))['total'];

$lowStock = mysqli_query($conn, "
    SELECT * FROM products 
    WHERE quantity <= low_stock_limit 
    ORDER BY quantity ASC
");

$products = mysqli_query($conn, "
    SELECT p.*, c.category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.category_id 
    ORDER BY p.product_id DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Stock Manager Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'nav.php'; ?>

<div class="container">
    <h1>Stock Manager Dashboard</h1>

    <p><b>Total Products:</b> <?php echo $totalProducts; ?></p>
    <p><b>Low Stock Items:</b> <span class="warning"><?php echo $lowStockCount; ?></span></p>

    <h2>Low Stock Alert</h2>
    <table>
        <tr>
            <th>Product</th>
            <th>Size</th>
            <th>Color</th>
            <th>Quantity</th>
            <th>Low Stock Limit</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($lowStock)) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
            <td><?php echo htmlspecialchars($row['size']); ?></td>
            <td><?php echo htmlspecialchars($row['color']); ?></td>
            <td class="warning"><?php echo htmlspecialchars($row['quantity']); ?></td>
            <td><?php echo htmlspecialchars($row['low_stock_limit']); ?></td>
        </tr>
        <?php } ?>
    </table>

    <h2>Product Stock List</h2>
    <table>
        <tr>
            <th>Name</th>
            <th>Category</th>
            <th>Size</th>
            <th>Color</th>
            <th>Quantity</th>
            <th>Price</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($products)) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
            <td><?php echo htmlspecialchars($row['category_name']); ?></td>
            <td><?php echo htmlspecialchars($row['size']); ?></td>
            <td><?php echo htmlspecialchars($row['color']); ?></td>
            <td><?php echo htmlspecialchars($row['quantity']); ?></td>
            <td>₱<?php echo number_format($row['price'], 2); ?></td>
        </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>