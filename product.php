
<?php
require_once 'config.php';
 
$p_id = $_GET['id'] ?? 0;
 
$stmt = $pdo->prepare("SELECT p.*, u.name as supplier_name FROM products p JOIN users u ON p.supplier_id = u.id WHERE p.id = ?");
$stmt->execute([$p_id]);
$product = $stmt->fetch();
 
if (!$product) {
    die("Product not found.");
}
 
// Handle Add to Cart
$cart_msg = '';
if (isset($_POST['add_to_cart'])) {
    if (!isLoggedIn()) {
        redirect('login.php');
    }
 
    $user_id = $_SESSION['user_id'];
    $qty = $_POST['quantity'] ?? 1;
 
    // Check if item already in cart
    $check = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $check->execute([$user_id, $p_id]);
    $item = $check->fetch();
 
    if ($item) {
        $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + ? WHERE id = ?");
        $stmt->execute([$qty, $item['id']]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $p_id, $qty]);
    }
    $cart_msg = "Product added to cart! <a href='cart.php' style='color: white; text-decoration: underline;'>View Cart</a>";
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($product['name']); ?> | Alibaba Clone</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
 
    <div class="container" style="padding: 40px 0;">
        <?php if ($cart_msg): ?>
            <div style="background: #28a745; color: white; padding: 15px; border-radius: 4px; margin-bottom: 20px; text-align: center;">
                <?php echo $cart_msg; ?>
            </div>
        <?php endif; ?>
 
        <div style="background: white; padding: 30px; border-radius: 8px; border: 1px solid #EEE; display: flex; gap: 40px;">
            <div style="width: 450px;">
                <img src="<?php echo $product['image'] ? h($product['image']) : 'https://via.placeholder.com/450x400?text=Product+Image'; ?>" alt="<?php echo h($product['name']); ?>" style="width: 100%; border-radius: 8px;">
            </div>
            <div style="flex: 1;">
                <div style="font-size: 14px; color: var(--light-text); margin-bottom: 10px;"><?php echo h($product['category']); ?></div>
                <h1 style="margin-bottom: 15px;"><?php echo h($product['name']); ?></h1>
 
                <div style="background: #F9F9F9; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                    <div style="font-size: 32px; font-weight: 700; color: var(--primary-color);">$<?php echo number_format($product['price'], 2); ?> <small style="font-size: 14px; color: #666; font-weight: 400;">/ Piece</small></div>
                    <div style="margin-top: 10px; font-size: 14px; color: #666;">MOQ: 1 Piece | <?php echo $product['quantity']; ?> Pieces available</div>
                </div>
 
                <div style="margin-bottom: 30px;">
                    <h4 style="margin-bottom: 10px;">Supplier Information</h4>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 40px; height: 40px; background: #EEE; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700;"><?php echo substr($product['supplier_name'], 0, 1); ?></div>
                        <div>
                            <strong><?php echo h($product['supplier_name']); ?></strong>
                            <div style="font-size: 12px; color: green;">Verified Supplier 💎</div>
                        </div>
                    </div>
                </div>
 
                <form action="product.php?id=<?php echo $p_id; ?>" method="POST">
                    <div class="form-group" style="width: 150px;">
                        <label for="quantity">Quantity</label>
                        <input type="number" name="quantity" id="quantity" value="1" min="1" max="<?php echo $product['quantity']; ?>">
                    </div>
                    <div style="display: flex; gap: 15px; margin-top: 20px;">
                        <button type="submit" name="add_to_cart" class="btn btn-primary" style="flex: 1; padding: 15px;">Add to Cart</button>
                        <button type="button" class="btn btn-outline" style="flex: 1;">Contact Supplier</button>
                    </div>
                </form>
 
                <div style="margin-top: 40px; border-top: 1px solid #EEE; padding-top: 20px;">
                    <h4>Product Description</h4>
                    <p style="margin-top: 10px; color: #444; white-space: pre-line;"><?php echo h($product['description']); ?></p>
                </div>
            </div>
        </div>
    </div>
 
    <?php include 'footer.php'; ?>
</body>
</html>
 
