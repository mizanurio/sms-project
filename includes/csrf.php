<?php
/**
 * csrf.php — CSRF Protection Functions
 *
 * Generates and verifies CSRF tokens to prevent cross-site request forgery attacks.
 * Every form that changes data (POST requests) must include a CSRF token.
 */

/**
 * Generate a CSRF token and store it in the session.
 * If a token already exists in the session, return the existing one.
 *
 * @return string The CSRF token (a random hex string)
 */
function csrf_token() {
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Generate a new token if one doesn't exist
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/**
 * Output a hidden HTML input field containing the CSRF token.
 * Call this inside every <form> that uses POST method.
 *
 * Usage in a form: <?php csrf_field(); ?>
 *
 * @return void Echoes the hidden input field
 */
function csrf_field() {
    echo '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

/**
 * Verify that the submitted CSRF token matches the session token.
 * Call this at the top of every POST request handler.
 *
 * @return bool True if the token is valid
 */
function csrf_verify() {
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check if the token was submitted and matches the session
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
        return false;
    }

    // Use hash_equals for timing-safe comparison (prevents timing attacks)
    $valid = hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);

    // Regenerate the token after verification to prevent reuse
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    return $valid;
}
