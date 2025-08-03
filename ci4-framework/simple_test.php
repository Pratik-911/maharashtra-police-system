<?php

// Simple test to check officers table and database connectivity
echo "=== Testing Officers Table ===\n";

// Database connection parameters (adjust as needed)
$host = 'localhost';
$username = 'root';
$password = 'root';
$database = 'police_duty_management';

try {
    // Connect to database
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Database connection successful\n";
    
    // Check if officers table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'officers'");
    if ($stmt->rowCount() > 0) {
        echo "✓ Officers table exists\n";
        
        // Check officers table structure
        $stmt = $pdo->query("DESCRIBE officers");
        echo "\nOfficers table structure:\n";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "  {$row['Field']} - {$row['Type']} - {$row['Null']} - {$row['Key']}\n";
        }
        
        // Count officers
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM officers");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "\n✓ Total officers in table: $count\n";
        
        // Show first few officers
        if ($count > 0) {
            $stmt = $pdo->query("SELECT id, name, badge_no FROM officers LIMIT 5");
            echo "\nFirst 5 officers:\n";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "  ID: {$row['id']}, Name: {$row['name']}, Badge: {$row['badge_no']}\n";
            }
        }
        
        // Check if officer IDs 1 and 2 exist (used in our test)
        $stmt = $pdo->prepare("SELECT id, name FROM officers WHERE id IN (1, 2)");
        $stmt->execute();
        $testOfficers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "\nTest officers (IDs 1, 2):\n";
        if (empty($testOfficers)) {
            echo "  ❌ No officers found with IDs 1 or 2\n";
        } else {
            foreach ($testOfficers as $officer) {
                echo "  ✓ ID: {$officer['id']}, Name: {$officer['name']}\n";
            }
        }
        
    } else {
        echo "❌ Officers table does not exist\n";
    }
    
    // Check duties table
    $stmt = $pdo->query("SHOW TABLES LIKE 'duties'");
    if ($stmt->rowCount() > 0) {
        echo "\n✓ Duties table exists\n";
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM duties");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "✓ Total duties in table: $count\n";
    } else {
        echo "\n❌ Duties table does not exist\n";
    }
    
    // Check duty_officers table
    $stmt = $pdo->query("SHOW TABLES LIKE 'duty_officers'");
    if ($stmt->rowCount() > 0) {
        echo "\n✓ Duty_officers table exists\n";
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM duty_officers");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "✓ Total duty_officers records: $count\n";
    } else {
        echo "\n❌ Duty_officers table does not exist\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ General error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";


// Test basic logging
echo "Testing log_message function...\n";
if (function_exists('log_message')) {
    log_message('info', 'TEST: Simple test script started - ' . date('Y-m-d H:i:s'));
    echo "✓ log_message function available\n";
} else {
    echo "✗ log_message function not available\n";
}

// Test file logging directly
$logFile = '/Users/pratik/Documents/Projects/trafficP2/ci4-framework/writable/logs/log-' . date('Y-m-d') . '.log';
$testMessage = "TEST: Direct file write - " . date('Y-m-d H:i:s') . "\n";

if (file_put_contents($logFile, $testMessage, FILE_APPEND | LOCK_EX)) {
    echo "✓ Direct file write successful\n";
} else {
    echo "✗ Direct file write failed\n";
}

echo "\nTest completed. Check the log file:\n";
echo "tail -5 $logFile\n";
