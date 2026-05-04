<?php
require_once 'config.php';
require_once 'auth.php';
checkCashierOrOwner();

$message = "";
$error = "";

if (isset($_POST['sell'])) {
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $qty = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
    $payment = trim($_POST['payment_method'] ?? '');
    $allowedPayments = ['Cash', 'GCash', 'Card'];

    if (!$product_id || !$qty || $qty < 1 || !in_array($payment, $allowedPayments, true)) {
        $error = "Please enter valid sale details.";
    } else {
        try {
            $conn->begin_transaction();

            $stmt = $conn->prepare("SELECT product_id, price, quantity FROM products WHERE product_id = ? FOR UPDATE");
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $product = $stmt->get_result()->fetch_assoc();

            if (!$product) {
                throw new Exception("Product not found.");
            }

            if ((int)$product['quantity'] < $qty) {
                throw new Exception("Not enough stock available.");
            }

            $price = (float)$product['price'];
            $subtotal = $price * $qty;

            $stmt = $conn->prepare("INSERT INTO sales (payment_method, total_amount) VALUES (?, ?)");
            $stmt->bind_param("sd", $payment, $subtotal);
            $stmt->execute();
            $sale_id = $conn->insert_id;

            $stmt = $conn->prepare("INSERT INTO sale_details (sale_id, product_id, quantity_sold, price, subtotal) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iiidd", $sale_id, $product_id, $qty, $price, $subtotal);
            $stmt->execute();

            $stmt = $conn->prepare("UPDATE products SET quantity = quantity - ? WHERE product_id = ?");
            $stmt->bind_param("ii", $qty, $product_id);
            $stmt->execute();

            $conn->commit();
            $message = "Sale successful. Stock updated automatically.";
        } catch (Exception $e) {
            if ($conn->errno === 0 || $conn->connect_errno === 0) {
                $conn->rollback();
            }
            error_log("Sale error: " . $e->getMessage());
            $error = $e->getMessage() === "Not enough stock available." ? $e->getMessage() : "Unable to save sale. Please try again.";
        }
    }
}

try {
    $products = $conn->query("SELECT product_id, product_name, size, color, price, quantity FROM products WHERE quantity > 0 ORDER BY product_name ASC");
} catch (mysqli_sql_exception $e) {
    error_log("Load cashier products error: " . $e->getMessage());
    $products = false;
    $error = "Unable to load products.";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cashier</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'nav.php'; ?>
<div class="container">
    <h1>Cashier Mode</h1>
    <?php if ($message): ?><p class="success"><?= e($message) ?></p><?php endif; ?>
    <?php if ($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?>

    <form method="POST">
        <label>Select Product</label>
        <select name="product_id" required>
            <option value="">Choose item</option>
            <?php if ($products): while ($row = $products->fetch_assoc()): ?>
                <option value="<?= e($row['product_id']) ?>">
                    <?= e($row['product_name'] . " - " . $row['size'] . " - " . $row['color'] . " - ₱" . $row['price'] . " - Stock: " . $row['quantity']) ?>
                </option>
            <?php endwhile; endif; ?>
        </select>

        <input type="number" name="quantity" placeholder="Quantity" min="1" required>
        <select name="payment_method" required>
            <option value="Cash">Cash</option>
            <option value="GCash">GCash</option>
            <option value="Card">Card</option>
        </select>
        <button type="submit" name="sell">Save Sale</button>
    </form>
</div>
</body>
</html>
