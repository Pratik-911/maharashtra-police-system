<?php
// Test password verification for officer passwords
$hash = '$2y$12$MGRDGYAmZp5OLdhmIqdmwu.a2EmKj2LuZgLkApt.gX6Shq8RnfWZK';

echo "Testing password verification:\n";
echo "Hash: " . $hash . "\n\n";

$passwords = ['admin123', 'password123', 'police123', '123456', 'officer123'];

foreach ($passwords as $password) {
    $result = password_verify($password, $hash) ? 'MATCH ✓' : 'NO MATCH ✗';
    echo "Password '$password': $result\n";
}

// Also test JWT library
echo "\n--- Testing JWT Library ---\n";
require_once '/Users/pratik/Documents/Projects/trafficP2/ci4-framework/vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

try {
    
    $payload = [
        'test' => 'data',
        'iat' => time(),
        'exp' => time() + 3600
    ];
    
    $secret = 'test_secret';
    $token = JWT::encode($payload, $secret, 'HS256');
    echo "JWT Token generated: " . substr($token, 0, 50) . "...\n";
    
    $decoded = JWT::decode($token, new Key($secret, 'HS256'));
    echo "JWT Token decoded successfully ✓\n";
    
} catch (Exception $e) {
    echo "JWT Error: " . $e->getMessage() . "\n";
}
?>
