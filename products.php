<?php
require_once 'config.php';
 
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
 
$sql = "SELECT p.*, u.name as supplier_name FROM products p JOIN users u ON p.supplier_id = u.id WHERE 1=1";
$params = [];
 
if ($search) {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
 
if ($category) {
    $sql .= " AND p.category = ?";
    $params[] = $category;
}
 
$sql .= " ORDER BY p.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Products | Alibaba Clone</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
 
    <div class="container" style="padding-top: 40px;">
        <div style="display: flex; gap: 30px;">
            <!-- Sidebar -->
            <aside style="width: 250px; flex-shrink: 0; background: white; padding: 20px; border-radius: 8px; height: fit-content; border: 1px solid #EEE;">
                <h3>Categories</h3>
                <ul style="margin-top: 15px;">
                    <li><a href="products.php" style="display: block; padding: 5px 0; <?php echo !$category ? 'color: var(--primary-color); font-weight: 600;' : ''; ?>">All Categories</a></li>
                    <?php
                    $cats = ['Electronics', 'Home & Garden', 'Apparel', 'Vehicles', 'Beauty', 'Sports'];
                    foreach ($cats as $c):
                    ?>
                        <li><a href="products.php?category=<?php echo urlencode($c); ?>" style="display: block; padding: 5px 0; <?php echo $category === $c ? 'color: var(--primary-color); font-weight: 600;' : ''; ?>"><?php echo $c; ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </aside>
 
            <!-- Main Content -->
            <div style="flex: 1;">
                <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #EEE; margin-bottom: 20px;">
                    <h2>
                        <?php 
                        if ($search) echo "Search results for \"" . h($search) . "\"";
                        elseif ($category) echo h($category);
                        else echo "All Products";
                        ?>
                    </h2>
                    <p style="color: #666; font-size: 14px;"><?php echo count($products); ?> items found</p>
                </div>
 
                <?php if (empty($products)): ?>
                    <div style="text-align: center; padding: 60px; background: white; border-radius: 8px;">
                        <span style="font-size: 48px;">🔍</span>
                        <p style="margin-top: 20px; color: #666;">No products matched your criteria. Try adjusting your filters.</p>
                    </div>
                <?php else: ?>
                    <div class="product-grid">
                        <?php foreach ($products as $product): ?>
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
        </div>
    </div>
 
    <?php include 'footer.php'; ?>
    <script src="script.js"></script>
</body>
</html>
 
