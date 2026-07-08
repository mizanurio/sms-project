<?php
/**
 * db.php — Database Connection Class
 *
 * Provides a singleton PDO connection to the MySQL database.
 * All queries should use prepared statements via this class.
 */

// Load configuration
require_once __DIR__ . '/config.php';

class Database {
    /**
     * @var PDO|null Holds the single database connection instance
     */
    private static $instance = null;

    /**
     * Get the database connection (singleton pattern).
     * Creates a new PDO connection if one doesn't exist yet.
     *
     * @return PDO The database connection object
     */
    public static function get_connection() {
        if (self::$instance === null) {
            try {
                // Build the DSN (Data Source Name) string for PDO
                $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

                // Set PDO options for security and error handling
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,    // Throw exceptions on errors
                    PDO::ATTR_DEFAULT_FETCH_MODE  => PDO::FETCH_ASSOC,         // Return associative arrays
                    PDO::ATTR_EMULATE_PREPARES    => false,                     // Use real prepared statements
                ];

                // Create the PDO connection
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                // Show a user-friendly error, never expose raw SQL errors
                error_log('Database connection failed: ' . $e->getMessage());
                die('Sorry, we could not connect to the database. Please try again later.');
            }
        }
        return self::$instance;
    }

    /**
     * Execute a query with parameters and return the PDOStatement.
     * Use this for INSERT, UPDATE, DELETE queries.
     *
     * @param string $sql    The SQL query with ? placeholders
     * @param array  $params The values to bind to the placeholders
     * @return PDOStatement   The executed statement
     */
    public static function query($sql, $params = []) {
        try {
            $pdo = self::get_connection();
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log('Query failed: ' . $e->getMessage());
            die('Sorry, a database error occurred. Please try again later.');
        }
    }

    /**
     * Fetch all rows from a SELECT query.
     *
     * @param string $sql    The SQL SELECT query with ? placeholders
     * @param array  $params The values to bind to the placeholders
     * @return array          An array of associative arrays (one per row)
     */
    public static function fetch_all($sql, $params = []) {
        $stmt = self::query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Fetch a single row from a SELECT query.
     *
     * @param string $sql    The SQL SELECT query with ? placeholders
     * @param array  $params The values to bind to the placeholders
     * @return array|false    An associative array for the row, or false if not found
     */
    public static function fetch_one($sql, $params = []) {
        $stmt = self::query($sql, $params);
        return $stmt->fetch();
    }

    /**
     * Get the ID of the last inserted row.
     *
     * @return string The last insert ID
     */
    public static function last_insert_id() {
        return self::get_connection()->lastInsertId();
    }

    /**
     * Get the number of rows affected by the last query.
     *
     * @param PDOStatement $stmt The statement to check
     * @return int                The number of affected rows
     */
    public static function row_count($stmt) {
        return $stmt->rowCount();
    }

    /**
     * Begin a database transaction.
     * Use this when you need multiple queries to succeed or fail together.
     */
    public static function begin_transaction() {
        self::get_connection()->beginTransaction();
    }

    /**
     * Commit the current transaction.
     */
    public static function commit() {
        self::get_connection()->commit();
    }

    /**
     * Roll back the current transaction.
     */
    public static function rollback() {
        self::get_connection()->rollBack();
    }
}
