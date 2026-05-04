<div class="nav">
    <?php if ($_SESSION['role'] == 'owner') { ?>
        <a href="dashboard.php">Dashboard</a>
        <a href="products.php">Products</a>
        <a href="categories.php">Categories</a>
        <a href="sales_report.php">Sales Report</a>
        <a href="backup.php">Backup</a>
    <?php } ?>
    <a href="cashier.php">Cashier</a>
    <a href="logout.php">Logout</a>
</div>
