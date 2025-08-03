<?php
/**
 * Maharashtra Police Duty Management System Setup Script
 * This script helps set up the application quickly
 */

echo "=== महाराष्ट्र पोलीस ड्यूटी व्यवस्थापन प्रणाली सेटअप ===\n\n";

// Check PHP version
if (version_compare(PHP_VERSION, '8.0.0') < 0) {
    die("Error: PHP 8.0 or higher is required. Current version: " . PHP_VERSION . "\n");
}

echo "✓ PHP Version: " . PHP_VERSION . " (OK)\n";

// Check required extensions
$required_extensions = ['mysqli', 'pdo', 'pdo_mysql', 'json', 'mbstring', 'curl'];
$missing_extensions = [];

foreach ($required_extensions as $ext) {
    if (!extension_loaded($ext)) {
        $missing_extensions[] = $ext;
    }
}

if (!empty($missing_extensions)) {
    die("Error: Missing PHP extensions: " . implode(', ', $missing_extensions) . "\n");
}

echo "✓ Required PHP extensions are available\n";

// Database configuration
echo "\n--- डेटाबेस कॉन्फिगरेशन ---\n";
echo "Please provide database connection details:\n";

$db_host = readline("Database Host (default: localhost): ") ?: 'localhost';
$db_user = readline("Database Username (default: root): ") ?: 'root';
$db_pass = readline("Database Password: ");
$db_name = readline("Database Name (default: police_duty_management): ") ?: 'police_duty_management';

// Test database connection
try {
    $pdo = new PDO("mysql:host=$db_host", $db_user, $db_pass);
    echo "✓ Database connection successful\n";
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✓ Database '$db_name' created/verified\n";
    
    // Use the database
    $pdo->exec("USE `$db_name`");
    
    // Import schema
    if (file_exists('database_schema.sql')) {
        $sql = file_get_contents('database_schema.sql');
        // Remove the CREATE DATABASE and USE statements from the file
        $sql = preg_replace('/CREATE DATABASE.*?;/i', '', $sql);
        $sql = preg_replace('/USE.*?;/i', '', $sql);
        
        $pdo->exec($sql);
        echo "✓ Database schema imported successfully\n";
    } else {
        echo "⚠ Warning: database_schema.sql not found. Please import manually.\n";
    }
    
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage() . "\n");
}

// Update database configuration file
$config_file = 'app/Config/Database.php';
if (file_exists($config_file)) {
    $config_content = file_get_contents($config_file);
    
    // Update database settings
    $config_content = preg_replace(
        "/'hostname'\s*=>\s*'[^']*'/", 
        "'hostname' => '$db_host'", 
        $config_content
    );
    $config_content = preg_replace(
        "/'username'\s*=>\s*'[^']*'/", 
        "'username' => '$db_user'", 
        $config_content
    );
    $config_content = preg_replace(
        "/'password'\s*=>\s*'[^']*'/", 
        "'password' => '$db_pass'", 
        $config_content
    );
    $config_content = preg_replace(
        "/'database'\s*=>\s*'[^']*'/", 
        "'database' => '$db_name'", 
        $config_content
    );
    
    file_put_contents($config_file, $config_content);
    echo "✓ Database configuration updated\n";
} else {
    echo "⚠ Warning: Database config file not found\n";
}

// Set permissions (if on Unix-like system)
if (DIRECTORY_SEPARATOR === '/') {
    $writable_dirs = ['writable', 'writable/cache', 'writable/logs', 'writable/session', 'writable/uploads'];
    
    foreach ($writable_dirs as $dir) {
        if (is_dir($dir)) {
            chmod($dir, 0755);
            echo "✓ Set permissions for $dir\n";
        }
    }
}

echo "\n=== सेटअप पूर्ण! ===\n";
echo "अॅप्लिकेशन चालवण्यासाठी:\n";
echo "1. php spark serve\n";
echo "2. ब्राउझरमध्ये http://localhost:8080 उघडा\n\n";

echo "डिफॉल्ट लॉगिन क्रेडेंशियल्स:\n";
echo "प्रशासक: admin / admin123\n";
echo "अधिकारी: MH001 / admin123\n\n";

echo "आनंदाने वापर करा! 🚔\n";
?>
