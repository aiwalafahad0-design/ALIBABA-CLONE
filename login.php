<?php
require_once 'config.php';
 
// Redirect if already logged in
if (isLoggedIn()) {
    redirect('dashboard.php');
}
 
$error = '';
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
 
    if (empty($email) || empty($password)) {
        $error = "Email and password are required.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
 
        if ($user && password_verify($password, $user['password'])) {
            // Login successful
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
 
            redirect('dashboard.php');
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Alibaba Clone</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
 
    <div class="container">
        <div class="auth-container">
            <h2>Welcome Back</h2>
            <?php if ($error): ?>
                <div style="color: red; margin-bottom: 15px; text-align: center;"><?php echo h($error); ?></div>
            <?php endif; ?>
 
            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Enter your password">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Sign In</button>
            </form>
            <p style="text-align: center; margin-top: 15px; font-size: 14px;">
                New to Alibaba Clone? <a href="signup.php" style="color: var(--primary-color);">Sign Up</a>
            </p>
        </div>
    </div>
 
    <?php include 'header.php'; ?>
    <script src="script.js"></script>
</body>
</html>
 
