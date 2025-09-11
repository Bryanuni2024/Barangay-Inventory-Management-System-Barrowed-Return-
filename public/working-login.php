<?php
session_start();

// Simple database connection
$host = '127.0.0.1';
$dbname = 'barangay_inventory';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle login
if ($_POST) {
    $input_username = $_POST['username'] ?? '';
    $input_password = $_POST['password'] ?? '';
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR name = ?");
    $stmt->execute([$input_username, $input_username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($input_password, $user['password'])) {
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        header('Location: working-dashboard.php');
        exit;
    } else {
        $error = 'Invalid credentials';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Working Login - Barangay Inventory</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0; 
            padding: 0; 
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            width: 400px;
            max-width: 90%;
        }
        .form-group { margin: 20px 0; }
        input { 
            padding: 15px; 
            width: 100%; 
            margin: 5px 0; 
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button { 
            padding: 15px 30px; 
            background: #27ae60; 
            color: white; 
            border: none; 
            cursor: pointer; 
            width: 100%;
            border-radius: 5px;
            font-size: 16px;
        }
        button:hover { background: #219a52; }
        .error { color: red; margin: 10px 0; padding: 10px; background: #ffe6e6; border-radius: 5px; }
        .credentials { 
            margin-top: 20px; 
            padding: 15px; 
            background: #f8f9fa; 
            border-radius: 5px; 
            font-size: 14px;
        }
        h2 { text-align: center; color: #333; margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Barangay Inventory System</h2>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <input type="text" name="username" placeholder="Username or Email" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <div class="form-group">
                <button type="submit">Sign In</button>
            </div>
        </form>
        
        <div class="credentials">
            <strong>Demo Credentials:</strong><br>
            Username: admin@barangay.com<br>
            Password: password
        </div>
        
        <div style="margin-top: 20px; text-align: center;">
            <a href="working-dashboard.php" style="color: #27ae60; text-decoration: none;">View Dashboard (No Login)</a>
        </div>
    </div>
</body>
</html>


