<?php
/**
 * Test Database Connection - QR Meter Reading System
 * This script tests the database connection without problematic PDO attributes
 */

// Include configuration
require_once 'config/config.php';

try {
    echo "Testing database connection...\n";
    
    // Get database connection
    $pdo = getDatabaseConnection();
    
    if ($pdo) {
        echo "✅ Database connection successful!\n";
        
        // Test basic query
        $stmt = $pdo->query("SELECT 1 as test");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result && $result['test'] == 1) {
            echo "✅ Basic query test passed!\n";
        } else {
            echo "❌ Basic query test failed!\n";
        }
        
        // Test transaction handling
        echo "Testing transaction handling...\n";
        
        try {
            $pdo->beginTransaction();
            echo "✅ Transaction started successfully\n";
            
            // Test a simple insert (we'll rollback)
            $stmt = $pdo->prepare("SELECT 1 as test");
            $stmt->execute();
            echo "✅ Query execution in transaction successful\n";
            
            $pdo->rollback();
            echo "✅ Transaction rollback successful\n";
            
        } catch (Exception $e) {
            echo "❌ Transaction test failed: " . $e->getMessage() . "\n";
            if ($pdo->inTransaction()) {
                $pdo->rollback();
            }
        }
        
        // Test isolation level setting
        try {
            $pdo->exec("SET TRANSACTION ISOLATION LEVEL READ COMMITTED");
            echo "✅ Isolation level setting successful\n";
        } catch (Exception $e) {
            echo "❌ Isolation level setting failed: " . $e->getMessage() . "\n";
        }
        
        echo "\n🎉 All database tests completed!\n";
        
    } else {
        echo "❌ Database connection failed!\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
