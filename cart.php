<?php
require_once 'config.php';
 
if (!isLoggedIn()) {
    redirect('login.php');
}
 
$user_id = $_SESSION['user_id'];
 
// Handle item removal
if (isset($_GET['remove'])) {
    $c_id = $_GET['remove'];
    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->execute([$c_id, $user_id]);
    redirect('cart.php');
}
 
// Fetch cart items
$stmt = $pdo->prepare("
    SELECT c.id as cart_id, c.quantity as cart_qty, p.* 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$cartItems = $stmt->fetchAll();
 
$totalPrice = 0;
foreach ($cartItems as $item) {
    $totalPrice += $item['price'] * $item['cart_qty'];
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart | Alibaba Clone</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
 
    <div class="container" style="padding: 40px 0;">
        <h1>Your Shopping Cart</h1>
 
        <?php if (empty($cartItems)): ?>
            <div style="text-align: center; padding: 60px; background: white; border-radius: 8px; margin-top: 20px;">
                <div style="font-size: 64px;">🛒</div>
                <h2 style="margin-top: 20px;">Your cart is empty</h2>
                <p style="color: #666; margin-bottom: 30px;">Find great deals on our products page.</p>
                <a href="products.php" class="btn btn-primary">Start Shopping</a>
            </div>
        <?php else: ?>
            <div style="display: flex; gap: 30px; margin-top: 30px;">
                <div style="flex: 2;">
                    <div class="dashboard-card" style="margin-top: 0;">
                        <table>
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cartItems as $item): ?>
                                    <tr>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 15px;">
                                                <img src="<?php echo $item['image'] ? h($item['image']) : 'https://via.placeholder.com/60'; ?>" alt="" style="width: 50px; height: 50px; border-radius: 4px; object-fit: cover;">
                                                <div>
                                                    <strong><?php echo h($item['name']); ?></strong><br>
                                                    <small style="color: #999;"><?php echo h($item['category']); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                                        <td><?php echo $item['cart_qty']; ?></td>
                                        <td>$<?php echo number_format($item['price'] * $item['cart_qty'], 2); ?></td>
                                        <td><a href="cart.php?remove=<?php echo $item['cart_id']; ?>" style="color: red;">Remove</a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
 
                <div style="flex: 1;">
                    <div class="dashboard-card" style="margin-top: 0; position: sticky; top: 100px;">
                        <h3>Order Summary</h3>
                        <div style="margin-top: 20px; display: flex; justify-content: space-between;">
                            <span>Subtotal:</span>
                            <span>$<?php echo number_format($totalPrice, 2); ?></span>
                        </div>
                        <div style="margin-top: 10px; display: flex; justify-content: space-between;">
                            <span>Shipping:</span>
                            <span style="color: green;">FREE</span>
                        </div>
                        <hr style="margin: 20px 0; border: none; border-top: 1px solid #EEE;">
                        <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 20px;">
                            <span>Total:</span>
                            <span style="color: var(--primary-color);">$<?php echo number_format($totalPrice, 2); ?></span>
                        </div>
                        <a href="checkout.php" class="btn btn-primary" style="width: 100%; margin-top: 30px; padding: 15px;">Proceed to Checkout</a>
                        <p style="text-align: center; margin-top: 15px; font-size: 12px; color: #999;">Trade Assurance protects your orders</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
 
    <?php include 'footer.php'; ?>
</body>
</html>
 
