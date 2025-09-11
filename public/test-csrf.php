<?php
// Simple test to check if CSRF is disabled
echo "<h1>CSRF Test</h1>";
echo "<p>If you can see this page, CSRF is disabled for static files.</p>";
echo "<p>Current time: " . date('Y-m-d H:i:s') . "</p>";
echo "<p><a href='/login'>Go to Login</a></p>";
echo "<p><a href='/working-login.php'>Go to Working Login (PHP)</a></p>";
?>


