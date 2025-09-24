<?php
/**
 * RMS QR Meter Reading System - Enhanced Database Class
 * Implements essential best practices while maintaining compatibility
 */

class Database {
    private $pdo;
    private $config;
    private static $instance = null;
    
    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct() {
        $this->loadConfiguration();
        $this->connect();
    }
    
    /**
     * Get singleton instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            // Check if constants are defined
            if (!defined('DB_SERVER') || !defined('DB_NAME') || !defined('DB_USER') || !defined('DB_PASSWORD')) {
                throw new Exception('Database configuration constants not available. Make sure config.php is included first.');
            }
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Load configuration from config.php constants
     */
    private function loadConfiguration() {
        $this->config = array(
            'host' => DB_SERVER,
            'database' => DB_NAME,
            'username' => DB_USER,
            'password' => DB_PASSWORD,
            'charset' => 'utf8',
            'port' => DB_PORT,
            'connection_timeout' => DB_CONNECTION_TIMEOUT,
            'query_timeout' => DB_QUERY_TIMEOUT,
            'max_retries' => DB_MAX_RETRIES,
            'pool_size' => DB_POOL_SIZE
        );
        
        // Log configuration (mask password in production)
        $logConfig = $this->config;
        $logConfig['password'] = '***';
        error_log("QR System Database Config: " . json_encode($logConfig));
    }
    
    /**
     * Establish database connection with enhanced error handling
     */
    private function connect() {
        try {
            $dsn = "sqlsrv:Server={$this->config['host']},{$this->config['port']};Database={$this->config['database']}";
            
            $options = array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            );
            
            $this->pdo = new PDO($dsn, $this->config['username'], $this->config['password'], $options);
            
            // Set additional SQL Server specific options
            $this->pdo->exec("SET QUOTED_IDENTIFIER ON");
            $this->pdo->exec("SET ANSI_NULLS ON");
            $this->pdo->exec("SET ANSI_WARNINGS ON");
            $this->pdo->exec("SET ARITHABORT ON");
            
            // Log successful connection
            error_log("QR System Database: Connected successfully to {$this->config['database']} on {$this->config['host']}");
            
        } catch (PDOException $e) {
            error_log("QR System Database Error: " . $e->getMessage());
            throw new Exception('Database connection failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Get PDO connection
     */
    public function getConnection() {
        return $this->pdo;
    }
    
    /**
     * Execute a query and return results
     */
    public function query($sql, $params = array()) {
        try {
            $stmt = $this->pdo->prepare($sql);
            if ($stmt) {
                $stmt->execute($params);
                return $stmt->fetchAll();
            }
            return array();
        } catch (PDOException $e) {
            error_log("QR System Query Error: " . $e->getMessage());
            throw new Exception('Database query failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Execute a query and return single row
     */
    public function querySingle($sql, $params = array()) {
        try {
            $stmt = $this->pdo->prepare($sql);
            if ($stmt) {
                $stmt->execute($params);
                return $stmt->fetch();
            }
            return null;
        } catch (PDOException $e) {
            error_log("QR System Query Error: " . $e->getMessage());
            throw new Exception('Database query failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Execute an INSERT, UPDATE, or DELETE query
     */
    public function execute($sql, $params = array()) {
        try {
            $stmt = $this->pdo->prepare($sql);
            if ($stmt) {
                return $stmt->execute($params);
            }
            return false;
        } catch (PDOException $e) {
            error_log("QR System Execute Error: " . $e->getMessage());
            throw new Exception('Database execute failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Get the last inserted ID
     */
    public function lastInsertId() {
        try {
            return $this->pdo->lastInsertId();
        } catch (Exception $e) {
            error_log("QR System Database: Failed to get last insert ID: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Begin a transaction with enhanced error handling
     */
    public function beginTransaction() {
        try {
            if ($this->pdo->inTransaction()) {
                error_log("QR System Database: Transaction already in progress, rolling back...");
                $this->pdo->rollBack();
            }
            
            $result = $this->pdo->beginTransaction();
            
            if ($result) {
                error_log("QR System Database: Transaction started successfully");
            }
            
            return $result;
            
        } catch (Exception $e) {
            error_log("QR System Database: Failed to begin transaction: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Commit a transaction with enhanced error handling
     */
    public function commit() {
        try {
            if (!$this->pdo->inTransaction()) {
                error_log("QR System Database: No active transaction to commit");
                return false;
            }
            
            $result = $this->pdo->commit();
            
            if ($result) {
                error_log("QR System Database: Transaction committed successfully");
            }
            
            return $result;
            
        } catch (Exception $e) {
            error_log("QR System Database: Failed to commit transaction: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Rollback a transaction with enhanced error handling
     */
    public function rollback() {
        try {
            if (!$this->pdo->inTransaction()) {
                error_log("QR System Database: No active transaction to rollback");
                return false;
            }
            
            $result = $this->pdo->rollback();
            
            if ($result) {
                error_log("QR System Database: Transaction rolled back successfully");
            }
            
            return $result;
            
        } catch (Exception $e) {
            error_log("QR System Database: Failed to rollback transaction: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Test database connection
     */
    public function testConnection() {
        try {
            $result = $this->querySingle('SELECT 1 as test');
            return $result && $result['test'] == 1;
        } catch (Exception $e) {
            error_log("QR System Database Connection Test Failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get database configuration info (for debugging)
     */
    public function getConfigInfo() {
        return array(
            'host' => $this->config['host'],
            'database' => $this->config['database'],
            'username' => $this->config['username'],
            'port' => $this->config['port'],
            'connected' => $this->pdo !== null
        );
    }
    
    /**
     * Close database connection and cleanup
     */
    public function close() {
        try {
            // Rollback any pending transactions
            if ($this->pdo && $this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            
            // Close connection
            $this->pdo = null;
            
            error_log("QR System Database: Connection closed successfully");
            
        } catch (Exception $e) {
            error_log("QR System Database: Error closing connection: " . $e->getMessage());
        }
    }
    
    /**
     * Destructor to ensure proper cleanup
     */
    public function __destruct() {
        $this->close();
    }
}

/**
 * Helper function to get database instance
 */
function getQRDatabase() {
    return Database::getInstance();
}

/**
 * Helper function to get PDO connection
 */
function getQRConnection() {
    return Database::getInstance()->getConnection();
}
?>
