<?php
/**
 * auth.php — Authentication Helper Functions
 *
 * Provides functions for user login, logout, registration,
 * session management, and role-based access control.
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

/**
 * Start the session if not already started.
 * Also checks for session timeout (30 minutes of inactivity).
 */
function init_session() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check for session timeout
    if (isset($_SESSION['last_activity'])) {
        $inactive_time = time() - $_SESSION['last_activity'];
        if ($inactive_time > SESSION_TIMEOUT) {
            // Session has expired — destroy it and redirect to login
            session_unset();
            session_destroy();
            session_start();
            flash('error', 'Your session has expired. Please log in again.');
            redirect(BASE_URL . '/index.php');
            exit;
        }
    }
    // Update last activity timestamp
    $_SESSION['last_activity'] = time();
}

/**
 * Check if a user is currently logged in.
 *
 * @return bool True if the user is logged in, false otherwise
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Require the user to be logged in.
 * If not logged in, redirect to the login page with an error message.
 */
function require_login() {
    init_session();
    if (!is_logged_in()) {
        flash('error', 'Please log in to access this page.');
        redirect(BASE_URL . '/index.php');
        exit;
    }
}

/**
 * Require the user to have a specific role.
 * If the user's role does not match, redirect to dashboard with an error.
 *
 * @param string|array $roles The required role(s) — can be a string or array
 */
function require_role($roles) {
    require_login();

    // Allow passing a single role as a string
    if (!is_array($roles)) {
        $roles = [$roles];
    }

    $user = current_user();
    if (!in_array($user['role'], $roles)) {
        flash('error', 'You do not have permission to access this page.');
        redirect(BASE_URL . '/dashboard.php');
        exit;
    }
}

/**
 * Get the currently logged-in user's data from the database.
 *
 * @return array|false The user's data as an associative array, or false if not found
 */
function current_user() {
    if (!is_logged_in()) {
        return false;
    }
    $sql = "SELECT id, username, email, role, is_active, created_at FROM users WHERE id = ?";
    return Database::fetch_one($sql, [$_SESSION['user_id']]);
}

/**
 * Attempt to log in a user with email and password.
 *
 * @param string $email    The user's email address
 * @param string $password The user's plain-text password
 * @return array           ['success' => bool, 'message' => string]
 */
function login_user($email, $password) {
    // Look up the user by email
    $sql = "SELECT id, username, email, password_hash, role, is_active FROM users WHERE email = ?";
    $user = Database::fetch_one($sql, [$email]);

    // Check if user exists
    if (!$user) {
        return ['success' => false, 'message' => 'Invalid email or password.'];
    }

    // Check if the account is active
    if (!$user['is_active']) {
        return ['success' => false, 'message' => 'This account has been deactivated. Please contact an administrator.'];
    }

    // Verify the password against the stored hash
    if (!password_verify($password, $user['password_hash'])) {
        return ['success' => false, 'message' => 'Invalid email or password.'];
    }

    // Regenerate session ID for security (prevents session fixation attacks)
    session_regenerate_id(true);

    // Store user data in the session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['last_activity'] = time();

    return ['success' => true, 'message' => 'Login successful.'];
}

/**
 * Log out the current user by destroying the session.
 */
function logout_user() {
    // Unset all session variables
    $_SESSION = [];

    // Delete the session cookie
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    // Destroy the session
    session_destroy();
}

/**
 * Register a new student account.
 * Creates both a user record and a student record in a single transaction.
 *
 * @param array $data  Associative array with keys:
 *                     username, email, password, first_name, last_name,
 *                     date_of_birth, gender, phone, address
 * @return array       ['success' => bool, 'message' => string]
 */
function register_student($data) {
    // Check if email already exists
    $existing = Database::fetch_one("SELECT id FROM users WHERE email = ?", [$data['email']]);
    if ($existing) {
        return ['success' => false, 'message' => 'An account with this email already exists.'];
    }

    // Check if username already exists
    $existing = Database::fetch_one("SELECT id FROM users WHERE username = ?", [$data['username']]);
    if ($existing) {
        return ['success' => false, 'message' => 'This username is already taken.'];
    }

    // Hash the password securely
    $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);

    // Generate a unique student number (STU + 3-digit number)
    $last_student = Database::fetch_one("SELECT student_number FROM students ORDER BY id DESC LIMIT 1");
    if ($last_student) {
        $last_num = intval(substr($last_student['student_number'], 3));
        $new_num = $last_num + 1;
    } else {
        $new_num = 1;
    }
    $student_number = 'STU' . str_pad($new_num, 3, '0', STR_PAD_LEFT);

    // Use a transaction to ensure both records are created together
    try {
        Database::begin_transaction();

        // Create the user account
        Database::query(
            "INSERT INTO users (username, email, password_hash, role, is_active) VALUES (?, ?, ?, 'student', 1)",
            [$data['username'], $data['email'], $password_hash]
        );
        $user_id = Database::last_insert_id();

        // Create the student record
        $enrollment_year = date('Y');
        Database::query(
            "INSERT INTO students (user_id, student_number, first_name, last_name, date_of_birth, gender, phone, address, enrollment_year) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $user_id,
                $student_number,
                $data['first_name'],
                $data['last_name'],
                $data['date_of_birth'] ?? null,
                $data['gender'] ?? null,
                $data['phone'] ?? null,
                $data['address'] ?? null,
                $enrollment_year
            ]
        );

        Database::commit();
        return ['success' => true, 'message' => 'Registration successful! You can now log in.'];
    } catch (Exception $e) {
        Database::rollback();
        error_log('Registration failed: ' . $e->getMessage());
        return ['success' => false, 'message' => 'Registration failed. Please try again.'];
    }
}
