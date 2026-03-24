<?php
require_once 'config.php';
 
if (!isLoggedIn()) {
    redirect('login.php');
}
 
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$name = $_SESSION['name'];
 
// Supplier-specific logic
$myProducts = [];
$supplierOrders = [];
if ($role === 'supplier') {
    // Fetch supplier's products
    $stmt = $pdo->prepare("SELECT * FROM products WHERE supplier_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $myProducts = $stmt->fetchAll();
 
    // Fetch orders for supplier's products
    $stmt = $pdo->prepare("
        SELECT o.id as order_id, o.created_at, u.name as buyer_name, p.name as product_name, oi.quantity, oi.price
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        JOIN orders o ON oi.order_id = o.id
        JOIN users u ON o.user_id = u.id
        WHERE p.supplier_id = ?
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $supplierOrders = $stmt->fetchAll();
} else {
    // Buyer-specific logic: Fetch my orders
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $myOrders = $stmt->fetchAll();
}
 
// Handle product deletion
if (isset($_GET['delete_product']) && $role === 'supplier') {
    $p_id = $_GET['delete_product'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ? AND supplier_id = ?");
    $stmt->execute([$p_id, $user_id]);
    redirect('dashboard.php');
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Alibaba Clone</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
 
    <div class="container">
        <div style="margin-top: 40px; display: flex; justify-content: space-between; align-items: center;">
            <h1>Welcome, <?php echo h($name); ?>!</h1>
            <span style="background: var(--primary-color); color: white; padding: 5px 15px; border-radius: 20px; text-transform: capitalize; font-weight: 600;"><?php echo $role; ?> Dashboard</span>
        </div>
 
        <?php if ($role === 'supplier'): ?>
            <!-- Supplier View -->
            <div class="dashboard-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2>My Products</h2>
                    <a href="add-product.php" class="btn btn-primary">+ Add New Product</a>
                </div>
 
                <?php if (empty($myProducts)): ?>
                    <p>You haven't listed any products yet.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($myProducts as $p): ?>
                                <tr>
                                    <td><?php echo h($p['name']); ?></td>
                                    <td><?php echo h($p['category']); ?></td>
                                    <td>$<?php echo number_format($p['price'], 2); ?></td>
                                    <td><?php echo $p['quantity']; ?></td>
                                    <td>
                                        <a href="product.php?id=<?php echo $p['id']; ?>" style="color: blue;">View</a> | 
                                        <a href="dashboard.php?delete_product=<?php echo $p['id']; ?>" style="color: red;" onclick="return confirm('Are you sure?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
 
            <div class="dashboard-card">
                <h2>Recent Orders</h2>
                <?php if (empty($supplierOrders)): ?>
                    <p>No orders received yet.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Buyer</th>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($supplierOrders as $o): ?>
                                <tr>
                                    <td>#<?php echo $o['order_id']; ?></td>
                                    <td><?php echo h($o['buyer_name']); ?></td>
                                    <td><?php echo h($o['product_name']); ?></td>
                                    <td><?php echo $o['quantity']; ?></td>
                                    <td>$<?php echo number_format($o['price'] * $o['quantity'], 2); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($o['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
 
        <?php else: ?>
            <!-- Buyer View -->
            <div class="dashboard-card">
                <h2>Product Recommendations</h2>
                <p>Browse our <a href="products.php" style="color: var(--primary-color);">Global Marketplace</a> to find leading suppliers.</p>
            </div>
 
            <div class="dashboard-card">
                <h2>My Orders</h2>
                <?php if (empty($myOrders)): ?>
                    <p>You haven't placed any orders yet. <a href="products.php">Start shopping!</a></p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Total Price</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($myOrders as $o): ?>
                                <tr>
                                    <td>#<?php echo $o['id']; ?></td>
                                    <td>$<?php echo number_format($o['total_price'], 2); ?></td>
                                    <td><span style="color: green; font-weight: 600;">Processing</span></td>
                                    <td><?php echo date('M d, Y', strtotime($o['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
 
    <?php include 'footer.php'; ?>
    <script src="script.js"></script>
</body>
</html>
 
