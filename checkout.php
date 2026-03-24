<?php
require_once 'config.php';
 
if (!isLoggedIn()) {
    redirect('login.php');
}
 
$user_id = $_SESSION['user_id'];
$success = false;
$order_id = 0;
 
// Fetch cart items to process order
$stmt = $pdo->prepare("
    SELECT c.quantity as cart_qty, p.* 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$cartItems = $stmt->fetchAll();
 
if (empty($cartItems)) {
    redirect('products.php');
}
 
$totalPrice = 0;
foreach ($cartItems as $item) {
    $totalPrice += $item['price'] * $item['cart_qty'];
}
 
// Transaction for order placement
try {
    $pdo->beginTransaction();
 
    // 1. Create order
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price) VALUES (?, ?)");
    $stmt->execute([$user_id, $totalPrice]);
    $order_id = $pdo->lastInsertId();
 
    // 2. Create order items and update product stock
    $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    $stmtUpdate = $pdo->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
 
    foreach ($cartItems as $item) {
        $stmtItem->execute([$order_id, $item['id'], $item['cart_qty'], $item['price']]);
        $stmtUpdate->execute([$item['cart_qty'], $item['id']]);
    }
 
    // 3. Clear cart
    $stmtClear = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmtClear->execute([$user_id]);
 
    $pdo->commit();
    $success = true;
} catch (Exception $e) {
    $pdo->rollBack();
    die("Checkout failed: " . $e->getMessage());
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation | Alibaba Clone</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
 
    <div class="container" style="padding: 80px 0; text-align: center;">
        <?php if ($success): ?>
            <div style="background: white; padding: 50px; border-radius: 8px; border: 1px solid #EEE; max-width: 600px; margin: 0 auto;">
                <div style="font-size: 80px; color: #28a745; margin-bottom: 20px;">✅</div>
                <h1 style="margin-bottom: 10px;">Order Placed Successfully!</h1>
                <p style="color: #666; font-size: 18px; margin-bottom: 30px;">Thank you for your business. Your order ID is <strong>#<?php echo $order_id; ?></strong>.</p>
                <div style="display: flex; gap: 15px; justify-content: center;">
                    <a href="dashboard.php" class="btn btn-primary" style="padding: 12px 30px;">Go to Dashboard</a>
                    <a href="products.php" class="btn btn-outline" style="padding: 12px 30px;">Continue Sourcing</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
 
    <?php include 'footer.php'; ?>
</body>
</html>
 
