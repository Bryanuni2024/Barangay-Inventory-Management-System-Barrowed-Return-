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
          <input type="password" name="password" placeholder="Password" required>
          <button type="submit">Sign In</button>
        </form>
        {{-- <div style="margin-top: 15px; padding: 10px; background: #f8f9fa; border-radius: 5px; font-size: 12px; color: #666;">
          <strong>Demo Credentials:</strong><br>
          Username: admin@barangay.com<br>
          Password: password
        </div> --}}
      </div>
    </div>
  </div>

  <script>
    // Prevent back button access to login page after logout
    window.addEventListener('pageshow', function(event) {
      // Check if the page was loaded from cache (back button)
      if (event.persisted) {
        // Force reload to ensure fresh authentication check
        window.location.reload();
      }
    });

    // Clear any cached data when page loads
    window.addEventListener('load', function() {
      // Clear browser cache for this page
      if (window.performance && window.performance.navigation.type === 1) {
        // Page was refreshed, clear any cached data
        sessionStorage.clear();
        localStorage.removeItem('user_session');
      }
    });

    // Prevent form resubmission on page refresh
    if (window.history.replaceState) {
      window.history.replaceState(null, null, window.location.href);
    }
  </script>
</body>
</html>


