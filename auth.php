<?php
function checkLogin() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['user_id'])) {
        header("Location: index.php");
        exit();
    }
}

function checkOwner() {
    checkLogin();

    if (($_SESSION['role'] ?? '') !== 'owner') {
        header("Location: index.php");
        exit();
    }
}

function checkCashierOrOwner() {
    checkLogin();

    $role = $_SESSION['role'] ?? '';
    if (!in_array($role, ['owner', 'cashier'], true)) {
        header("Location: index.php");
        exit();
    }
}

function checkStockManagerOrOwner() {
    checkLogin();

    $role = $_SESSION['role'] ?? '';
    if (!in_array($role, ['owner', 'stock_manager'], true)) {
        header("Location: index.php");
        exit();
    }
}
?>