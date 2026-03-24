<?php
require_once 'config.php';
 
// Fetch featured products
$stmt = $pdo->query("SELECT p.*, u.name as supplier_name FROM products p JOIN users u ON p.supplier_id = u.id ORDER BY p.created_at DESC LIMIT 8");
$featuredProducts = $stmt->fetchAll();
 
// Categories placeholder
$categories = ['Electronics', 'Home & Garden', 'Apparel', 'Vehicles', 'Beauty', 'Sports'];
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alibaba Clone | Global B2B Marketplace</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
 
    <main>
        <!-- Hero Section -->
        <section class="hero" style="background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('https://source.unsplash.com/1600x600/?warehouse,logistics'); background-size: cover; height: 400px; display: flex; align-items: center; justify-content: center; color: white; text-align: center;">
            <div class="container">
                <h1 style="font-size: 48px; margin-bottom: 20px;">The Leading B2B Marketplace</h1>
                <p style="font-size: 20px; margin-bottom: 30px;">Connecting Global Buyers with Leading Suppliers</p>
                <a href="products.php" class="btn btn-primary" style="padding: 15px 40px; font-size: 18px;">Source Now</a>
            </div>
        </section>
 
        <!-- Categories Section -->
        <section style="padding: 60px 0; background-color: white;">
            <div class="container">
                <h2 style="margin-bottom: 30px;">One Request, Multiple Quotations</h2>
                <div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 20px;">
                    <?php foreach ($categories as $cat): ?>
                        <div style="text-align: center; border: 1px solid #EEE; padding: 20px; border-radius: 8px; transition: var(--transition);" onmouseover="this.style.borderColor='var(--primary-color)'" onmouseout="this.style.borderColor='#EEE'">
                            <div style="font-size: 30px; margin-bottom: 10px;">📦</div>
                            <div style="font-weight: 500;"><?php echo $cat; ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
 
        <!-- Featured Products -->
        <section style="padding: 60px 0;">
            <div class="container">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                    <h2>New Arrivals</h2>
                    <a href="products.php" style="color: var(--primary-color); font-weight: 600;">View All</a>
                </div>
 
                <?php if (empty($featuredProducts)): ?>
                    <p style="text-align: center; color: #666; padding: 40px;">No products found. Start listing items as a supplier!</p>
                <?php else: ?>
                    <div class="product-grid">
                        <?php foreach ($featuredProducts as $product): ?>
                            <div class="product-card">
                                <img src="<?php echo $product['image'] ? h($product['image']) : 'https://via.placeholder.com/220x180?text=Product'; ?>" alt="<?php echo h($product['name']); ?>" class="product-image">
                                <div class="product-info">
                                    <div class="product-category"><?php echo h($product['category']); ?></div>
                                    <a href="product.php?id=<?php echo $product['id']; ?>" class="product-name"><?php echo h($product['name']); ?></a>
                                    <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                                    <div style="font-size: 12px; color: #999; margin-top: 5px;">Supplier: <?php echo h($product['supplier_name']); ?></div>
                                    <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-outline" style="width: 100%; margin-top: 15px; font-size: 14px;">View Details</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>
 
    <?php include 'footer.php'; ?>
    <script src="script.js"></script>
</body>
</html>
