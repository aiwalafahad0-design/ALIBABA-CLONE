<?php
require_once 'config.php';
 
if (!isLoggedIn() || getRole() !== 'supplier') {
    redirect('login.php');
}
 
$error = '';
$success = '';
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $category = $_POST['category'] ?? '';
    $price = $_POST['price'] ?? 0;
    $quantity = $_POST['quantity'] ?? 0;
    $description = $_POST['description'] ?? '';
    $image_url = $_POST['image_url'] ?? ''; // Simple URL-based image for this project
 
    if (empty($name) || empty($category) || $price <= 0 || $quantity <= 0) {
        $error = "Please fill in all required fields with valid data.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO products (supplier_id, name, category, price, quantity, description, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$_SESSION['user_id'], $name, $category, $price, $quantity, $description, $image_url])) {
            $success = "Product added successfully! <a href='dashboard.php'>View in Dashboard</a>";
        } else {
            $error = "Failed to add product. Please try again.";
        }
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product | Alibaba Clone</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
 
    <div class="container">
        <div class="auth-container" style="max-width: 600px;">
            <h2>List New Product</h2>
            <?php if ($error): ?>
                <div style="color: red; margin-bottom: 15px; text-align: center;"><?php echo h($error); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div style="color: green; margin-bottom: 15px; text-align: center;"><?php echo $success; ?></div>
            <?php endif; ?>
 
            <form action="add-product.php" method="POST">
                <div class="form-group">
                    <label for="name">Product Name *</label>
                    <input type="text" id="name" name="name" required placeholder="e.g. Industrial Steel Pipes">
                </div>
                <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <label for="category">Category *</label>
                        <select id="category" name="category" required>
                            <option value="">Select Category</option>
                            <option value="Electronics">Electronics</option>
                            <option value="Home & Garden">Home & Garden</option>
                            <option value="Apparel">Apparel</option>
                            <option value="Vehicles">Vehicles</option>
                            <option value="Beauty">Beauty</option>
                            <option value="Sports">Sports</option>
                        </select>
                    </div>
                    <div>
                        <label for="price">Price (USD) *</label>
                        <input type="number" id="price" name="price" step="0.01" required placeholder="0.00">
                    </div>
                </div>
                <div class="form-group">
                    <label for="quantity">Available Quantity *</label>
                    <input type="number" id="quantity" name="quantity" required placeholder="100">
                </div>
                <div class="form-group">
                    <label for="image_url">Image URL (Optional)</label>
                    <input type="url" id="image_url" name="image_url" placeholder="https://example.com/image.jpg">
                </div>
                <div class="form-group">
                    <label for="description">Product Description</label>
                    <textarea id="description" name="description" rows="4" placeholder="Describe your product specs, MOQ, etc."></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Post Product</button>
            </form>
            <p style="text-align: center; margin-top: 15px; font-size: 14px;">
                <a href="dashboard.php" style="color: #666;">← Back to Dashboard</a>
            </p>
        </div>
    </div>
 
    <?php include 'footer.php'; ?>
    <script src="script.js"></script>
</body>
</html>
 
