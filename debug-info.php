<?php
// Display basic server information
echo "<h2>Server Information</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Current Path: " . __DIR__ . "<br>";

// Check extensions
echo "<h2>Required Extensions</h2>";
$required_extensions = ['curl', 'json', 'openssl', 'mbstring'];
foreach ($required_extensions as $ext) {
    echo "$ext: " . (extension_loaded($ext) ? "Loaded ✓" : "Not Loaded ✗") . "<br>";
}

// Check file paths
echo "<h2>File Paths</h2>";
echo "Vendor directory exists: " . (file_exists(__DIR__ . '/vendor') ? 'Yes ✓' : 'No ✗') . "<br>";
echo "Autoload file exists: " . (file_exists(__DIR__ . '/vendor/autoload.php') ? 'Yes ✓' : 'No ✗') . "<br>";
echo "Google Client exists: " . (file_exists(__DIR__ . '/vendor/google/apiclient/src/Google/Client.php') ? 'Yes ✓' : 'No ✗') . "<br>";
echo "OAuth2 service exists: " . (file_exists(__DIR__ . '/vendor/google/apiclient-services/src/Oauth2.php') ? 'Yes ✓' : 'No ✗') . "<br>";

// Check memory limits
echo "<h2>PHP Configuration</h2>";
echo "Memory Limit: " . ini_get('memory_limit') . "<br>";
echo "Max Execution Time: " . ini_get('max_execution_time') . " seconds<br>";
echo "Display Errors: " . (ini_get('display_errors') ? 'On' : 'Off') . "<br>";
echo "Error Reporting Level: " . ini_get('error_reporting') . "<br>";
?>