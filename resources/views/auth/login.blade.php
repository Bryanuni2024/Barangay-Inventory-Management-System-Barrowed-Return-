<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
  <title>Login</title>
  <link rel="stylesheet" href="{{ asset('inventory/assets/style.css') }}">
  <link rel="icon" type="image/jpg" href="{{ asset('inventory/assets/logo.jpg') }}">
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      display: flex;
      height: 100vh;
    }

    .login-wrapper {
      display: flex;
      width: 100%;
    }

    .login-image {
      flex: 1;
      background: url('{{ asset('inventory/assets/brgy1.jpg') }}') no-repeat center center;
      background-size: cover;
    }

    .login-box {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      background: #0abdea;
    }

    .login-form-container {
      background: #fff;
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
      width: 350px;
      text-align: center;
    }

    .login-form-container h2 {
      margin-bottom: 20px;
      color: #333;
    }

    .login-form-container input {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
    }

    .password-wrapper {
      position: relative;
    }

    .password-wrapper input {
      width: 100%;
    }

    .toggle-password {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      font-size: 14px;
      color: #888;
    }

    .login-form-container button {
      width: 100%;
      padding: 12px;
      background: #3498db;
      color: #fff;
      border: none;
      margin-left: 14px;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s;
    }

    .login-form-container button:hover {
      background: #2980b9;
    }

    .error {
      color: red;
      margin-bottom: 10px;
      font-size: 14px;
    }

    .success {
      color: green;
      margin-bottom: 10px;
      font-size: 14px;
    }
  </style>
</head>
<body>
  <div class="login-wrapper">
    <div class="login-image"></div>
    <div class="login-box">
      <div class="login-form-container">
        <h2>Sign In</h2>
        @if(session('error'))
          <div class="error">{{ session('error') }}</div>
        @endif
        @if(session('success'))
          <div class="success">{{ session('success') }}</div>
        @endif
        <form method="POST" action="/test-login">
          <input type="text" name="username" placeholder="Username or Email" required>
          
          <div class="password-wrapper">
            <input type="password" id="password" name="password" placeholder="Password" required>
            <span class="toggle-password" onclick="togglePassword()">👁</span>
          </div>
          
          <button  type="submit">Sign In</button>
        </form>
      </div>
    </div>
  </div>

  <script>
    // Show/Hide password toggle
    function togglePassword() {
      const passwordInput = document.getElementById("password");
      const toggleIcon = document.querySelector(".toggle-password");
      if (passwordInput.type === "password") {
        passwordInput.type = "text";
        toggleIcon.textContent = "🙈";
      } else {
        passwordInput.type = "password";
        toggleIcon.textContent = "👁";
      }
    }

    // Prevent back button access to login page after logout
    window.addEventListener('pageshow', function(event) {
      if (event.persisted) {
        window.location.reload();
      }
    });

    window.addEventListener('load', function() {
      if (window.performance && window.performance.navigation.type === 1) {
        sessionStorage.clear();
        localStorage.removeItem('user_session');
      }
    });

    if (window.history.replaceState) {
      window.history.replaceState(null, null, window.location.href);
    }
  </script>
</body>
</html>
