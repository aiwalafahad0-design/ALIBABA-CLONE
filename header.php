
<?php require_once 'config.php'; ?>
<header>
    <div class="container">
        <div class="nav-wrapper">
            <a href="index.php" class="logo">Alibaba<span>.com</span> <small style="font-size: 12px; color: #666;">Clone</small></a>
 
            <form action="products.php" method="GET" class="search-bar">
                <input type="text" name="search" placeholder="What are you looking for?" value="<?php echo h($_GET['search'] ?? ''); ?>">
                <button type="submit">Search</button>
            </form>
 
            <nav class="nav-links">
                <a href="index.php">Home</a>
                <a href="products.php">Products</a>
                <?php if (isLoggedIn()): ?>
                    <a href="dashboard.php">Dashboard</a>
                    <a href="cart.php">Cart</a>
                    <a href="logout.php" class="btn btn-outline" style="padding: 5px 15px;">Logout</a>
                <?php else: ?>
                    <a href="login.php">Sign In</a>
                    <a href="signup.php" class="btn btn-primary" style="padding: 5px 15px; color: white;">Join Free</a>
                <?php endif; ?>
            </nav>
        </div>
    </div>
</header>
 
