<?php
/**
 * logout.php — Logout Handler
 *
 * Destroys the user's session and redirects to the login page.
 */

// Include required files
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Start session if needed
init_session();

// Log the user out (destroys session)
logout_user();

// Start a new session so we can set a flash message
session_start();

// Set a success message
flash('success', 'You have been logged out successfully.');

// Redirect to the login page
redirect(BASE_URL . '/index.php');
