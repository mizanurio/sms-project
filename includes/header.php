<?php
/**
 * header.php — Shared Page Header
 *
 * Included at the top of every page. Provides:
 * - HTML head with Bootstrap 5 CSS and custom styles
 * - Dark navigation bar with role-based menu items
 * - Flash message display area
 */

// Include required files
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/functions.php';

// Initialise the session and check for timeout
init_session();

// Get the current user if logged in
$current_user = is_logged_in() ? current_user() : null;

// Set the page title (pages can set $page_title before including header)
$page_title = isset($page_title) ? $page_title : 'Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMS &mdash; <?php echo sanitize($page_title); ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Custom Stylesheet -->
    <link href="<?php echo BASE_URL; ?>/../assets/css/style.css" rel="stylesheet">
</head>
<body>

<!-- ============================================================ -->
<!-- NAVIGATION BAR                                                -->
<!-- Dark Bootstrap navbar with role-based menu items              -->
<!-- ============================================================ -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <!-- Brand / Logo -->
        <a class="navbar-brand fw-bold" href="<?php echo BASE_URL; ?>/dashboard.php">
            <i class="bi bi-mortarboard-fill me-2"></i>SMS
        </a>

        <!-- Mobile hamburger toggle button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <!-- Left-side navigation links -->
            <ul class="navbar-nav me-auto">
                <?php if ($current_user): ?>
                    <!-- Dashboard link (all roles) -->
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/dashboard.php">
                            <i class="bi bi-speedometer2 me-1"></i>Dashboard
                        </a>
                    </li>

                    <?php if ($current_user['role'] === 'admin'): ?>
                        <!-- Admin-only navigation items -->
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>/students/list.php">
                                <i class="bi bi-people me-1"></i>Students
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>/teachers/list.php">
                                <i class="bi bi-person-badge me-1"></i>Teachers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>/courses/list.php">
                                <i class="bi bi-book me-1"></i>Courses
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>/enrollments/list.php">
                                <i class="bi bi-card-checklist me-1"></i>Enrollments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>/grades/list.php">
                                <i class="bi bi-award me-1"></i>Grades
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>/attendance/mark.php">
                                <i class="bi bi-calendar-check me-1"></i>Attendance
                            </a>
                        </li>
                    <?php elseif ($current_user['role'] === 'teacher'): ?>
                        <!-- Teacher navigation items -->
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>/grades/list.php">
                                <i class="bi bi-award me-1"></i>Grades
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>/attendance/mark.php">
                                <i class="bi bi-calendar-check me-1"></i>Attendance
                            </a>
                        </li>
                    <?php endif; ?>
                    <!-- Student role: only Dashboard shown above -->
                <?php endif; ?>
            </ul>

            <!-- Right-side navigation (profile and logout) -->
            <ul class="navbar-nav ms-auto">
                <?php if ($current_user): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/profile.php">
                            <i class="bi bi-person-circle me-1"></i>Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/logout.php">
                            <i class="bi bi-box-arrow-right me-1"></i>Logout
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/index.php">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/register.php">
                            <i class="bi bi-person-plus me-1"></i>Register
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- ============================================================ -->
<!-- MAIN CONTENT AREA                                             -->
<!-- ============================================================ -->
<main class="container my-4">

    <?php
    // Display flash messages if any exist
    $flash_types = ['success', 'error', 'warning', 'info'];
    foreach ($flash_types as $type) {
        $message = get_flash($type);
        if ($message) {
            // Map 'error' to Bootstrap's 'danger' class
            $alert_class = ($type === 'error') ? 'danger' : $type;
            echo '<div class="alert alert-' . $alert_class . ' alert-dismissible fade show" role="alert">';
            echo sanitize($message);
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
        }
    }
    ?>
