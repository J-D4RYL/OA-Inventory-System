<?php
include 'config.php';
include 'auth.php';
checkCashierOrOwner();

$message = "";
$error = "";
$receipt = null;

/* SAVE SALE */
if (isset($_POST['sell'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $payment_method = $_POST['payment_method'];

    if (empty($product_id) || empty($quantity) || empty($payment_method)) {
        $error = "Please complete all fields.";
    } elseif ($quantity <= 0) {
        $error = "Quantity must be greater than zero.";
    } else {
        try {
            $productQuery = mysqli_query($conn, "SELECT * FROM products WHERE product_id = '$product_id'");
            $product = mysqli_fetch_assoc($productQuery);

            if (!$product) {
                throw new Exception("Product not found.");
            }

            if ($quantity > $product['quantity']) {
                throw new Exception("Not enough stock available.");
            }

            $price = $product['price'];
            $subtotal = $price * $quantity;

            mysqli_query($conn, "
                INSERT INTO sales (payment_method, total_amount)
                VALUES ('$payment_method', '$subtotal')
            ");

            $sale_id = mysqli_insert_id($conn);

            mysqli_query($conn, "
                INSERT INTO sale_details (sale_id, product_id, quantity_sold, price, subtotal)
                VALUES ('$sale_id', '$product_id', '$quantity', '$price', '$subtotal')
            ");

            mysqli_query($conn, "
                UPDATE products 
                SET quantity = quantity - $quantity
                WHERE product_id = '$product_id'
            ");

            header("Location: cashier.php?receipt_id=$sale_id&success=1");
            exit;

        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

/* DISPLAY RECEIPT */
if (isset($_GET['receipt_id'])) {
    $receipt_id = $_GET['receipt_id'];

    $receiptQuery = mysqli_query($conn, "
        SELECT s.sale_id, s.sale_date, s.payment_method, s.total_amount,
               p.product_name, p.size, p.color, sd.quantity_sold, sd.price, sd.subtotal
        FROM sales s
        JOIN sale_details sd ON s.sale_id = sd.sale_id
        JOIN products p ON sd.product_id = p.product_id
        WHERE s.sale_id = '$receipt_id'
    ");

    $receipt = mysqli_fetch_assoc($receiptQuery);
}

if (isset($_GET['success'])) {
    $message = "Sale completed successfully.";
}

$products = mysqli_query($conn, "
    SELECT * FROM products 
    WHERE quantity > 0 
    AND is_deleted = 0
    ORDER BY product_name ASC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cashier</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="receipt.css">
</head>
<body>

<?php include 'nav.php'; ?>

<div class="container">
    <h1>Cashier</h1>

    <?php if ($message != "") { ?>
        <p class="success"><?php echo $message; ?></p>
    <?php } ?>

    <?php if ($error != "") { ?>
        <p class="error"><?php echo $error; ?></p>
    <?php } ?>

    <form method="POST">
        <label>Select Product</label>
        <select name="product_id" required>
            <option value="">Choose Product</option>

            <?php while ($row = mysqli_fetch_assoc($products)) { ?>
                <option value="<?php echo $row['product_id']; ?>">
                    <?php 
                    echo $row['product_name'] . " - " . 
                         $row['size'] . " - " . 
                         $row['color'] . " - ₱" . 
                         number_format($row['price'], 2) . 
                         " - Stock: " . $row['quantity']; 
                    ?>
                </option>
            <?php } ?>
        </select>

        <label>Quantity</label>
        <input type="number" name="quantity" min="1" required>

        <label>Payment Method</label>
        <select name="payment_method" required>
            <option value="">Select Payment</option>
            <option value="Cash">Cash</option>
            <option value="GCash">GCash</option>
            <option value="Card">Card</option>
        </select>

        <button type="submit" name="sell">Save Sale</button>
    </form>
</div>

<?php if ($receipt != null) { ?>
<div class="popup-overlay" id="receiptPopup">
    <div class="popup-box receipt-box">

        <div id="receiptContent">
            <h3>OA Clothing</h3>
            <p><b>Receipt No:</b> <?php echo $receipt['sale_id']; ?></p>
            <p><b>Date:</b> <?php echo $receipt['sale_date']; ?></p>
            <p><b>Payment:</b> <?php echo $receipt['payment_method']; ?></p>

            <hr>

            <p><b>Product:</b> <?php echo $receipt['product_name']; ?></p>
            <p><b>Size:</b> <?php echo $receipt['size']; ?></p>
            <p><b>Color:</b> <?php echo $receipt['color']; ?></p>
            <p><b>Quantity:</b> <?php echo $receipt['quantity_sold']; ?></p>
            <p><b>Price:</b> ₱<?php echo number_format($receipt['price'], 2); ?></p>

            <hr>

            <h3>Total: ₱<?php echo number_format($receipt['subtotal'], 2); ?></h3>
            <p class="thank-you">Thank you for shopping!</p>
        </div>

        <button onclick="window.print()">Print Receipt</button>
        <button onclick="window.location.href='cashier.php'">Close</button>
    </div>
</div>
<?php } ?>

</body>
</html>