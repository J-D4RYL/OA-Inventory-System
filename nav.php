<div class="nav">
    <?php if ($_SESSION['role'] == 'owner') { ?>
        <a href="dashboard.php">Dashboard</a>
        <a href="products.php">Products</a>
        <a href="deleted_products.php">Deleted Products</a>
        <a href="categories.php">Categories</a>
        <a href="sales_report.php">Sales Report</a>
        <a href="backup.php">Backup</a>
        <a href="cashier.php">Cashier</a>
    <?php } ?>

    <?php if ($_SESSION['role'] == 'cashier') { ?>
        <a href="cashier.php">Cashier</a>
    <?php } ?>

    <?php if ($_SESSION['role'] == 'stock_manager') { ?>
        <a href="stock_dashboard.php">Stock Dashboard</a>
        <a href="products.php">Products</a>
        <a href="deleted_products.php">Deleted Products</a>
    <?php } ?>

    <a href="logout.php">Logout</a>
</div>