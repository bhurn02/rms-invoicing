<?php
/**
 * RMS QR Meter Reading System - Database Class
 * Provides PDO-based database connectivity for the QR meter reading system
 * Integrates with existing RMS configuration
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
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Load configuration from RMS config files
     */
    private function loadConfiguration() {
        // Load configuration directly (avoid circular dependency)
        $this->config = array(
            'host' => 'localhost',
            'database' => 'RMS',
            'username' => 'web_app',
            'password' => '@webapp123',
            'charset' => 'utf8'
        );
        
        // Log configuration for debugging
        error_log("QR System Database Config: " . json_encode($this->config));
    }
    
    /**
     * Establish database connection using PDO
     */
    private function connect() {
        try {
            $dsn = "sqlsrv:Server={$this->config['host']};Database={$this->config['database']}";
            
            $options = array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            );
            
            $this->pdo = new PDO($dsn, $this->config['username'], $this->config['password'], $options);
            
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
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Begin a transaction
     */
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }
    
    /**
     * Commit a transaction
     */
    public function commit() {
        return $this->pdo->commit();
    }
    
    /**
     * Rollback a transaction
     */
    public function rollback() {
        return $this->pdo->rollback();
    }
    
    /**
     * Test database connection
     */
    public function testConnection() {
        try {
            $result = $this->querySingle('SELECT 1 as test');
            return $result && $result['test'] == 1;
        } catch (Exception $e) {
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
            'connected' => $this->pdo !== null
        );
    }
    
    /**
     * Close database connection
     */
    public function close() {
        $this->pdo = null;
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
