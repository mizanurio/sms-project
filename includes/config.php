<?php
/**
 * config.php — Application Configuration
 *
 * Contains all configuration constants for the SMS application.
 * Update these values to match your local environment.
 */

// Buffer all output so header() calls in redirect() work after HTML is output
if (ob_get_level() === 0) {
    ob_start();
}

// --- Database Configuration ---
define('DB_HOST', 'localhost');
define('DB_NAME', 'sms_database');
define('DB_USER', 'root');
define('DB_PASS', '');        // Default XAMPP has no password
define('DB_CHARSET', 'utf8mb4');

// --- Application Configuration ---
// Base URL — update if your folder name or port differs
define('BASE_URL', '/sms-project/public');

// --- Session Configuration ---
// Session timeout in seconds (30 minutes)
define('SESSION_TIMEOUT', 1800);

// --- Application Info ---
define('APP_NAME', 'SMS');
define('APP_FULL_NAME', 'Student Management System');
define('APP_VERSION', '1.0.0');
