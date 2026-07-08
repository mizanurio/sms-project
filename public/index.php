<?php
/**
 * index.php — Login Page
 *
 * The main entry point for the SMS application.
 * Displays a login form and handles authentication.
 */

// Set the page title
$page_title = 'Login';

// Include the header (starts session, loads all includes)
require_once __DIR__ . '/../includes/header.php';

// If user is already logged in, redirect to dashboard
if (is_logged_in()) {
    redirect(BASE_URL . '/dashboard.php');
}

// Initialise variables for form data and errors
$email = '';
$errors = [];

// ============================================================
// Handle form submission (POST request)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Verify CSRF token
    if (!csrf_verify()) {
        $errors[] = 'Invalid form submission. Please try again.';
    } else {
        // Get and sanitise form inputs
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validate required fields
        if (empty($email)) {
            $errors[] = 'Email address is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }

        if (empty($password)) {
            $errors[] = 'Password is required.';
        }

        // Attempt login if no validation errors
        if (empty($errors)) {
            $result = login_user($email, $password);
            if ($result['success']) {
                flash('success', 'Welcome back! You are now logged in.');
                redirect(BASE_URL . '/dashboard.php');
            } else {
                $errors[] = $result['message'];
            }
        }
    }
}
?>

<!-- ============================================================ -->
<!-- LOGIN FORM                                                    -->
<!-- Centered card layout for the login form                       -->
<!-- ============================================================ -->
<div class="row justify-content-center">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-sm mt-5">
            <div class="card-body p-4">
                <h2 class="card-title text-center mb-4">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Login
                </h2>

                <!-- Display validation errors if any -->
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo sanitize($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Login form -->
                <form method="POST" action="<?php echo BASE_URL; ?>/index.php">
                    <!-- CSRF token — required for security -->
                    <?php csrf_field(); ?>

                    <!-- Email field -->
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            Email Address <span class="text-danger">*</span>
                        </label>
                        <input type="email"
                               class="form-control"
                               id="email"
                               name="email"
                               value="<?php echo sanitize($email); ?>"
                               placeholder="Enter your email"
                               required>
                    </div>

                    <!-- Password field -->
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            Password <span class="text-danger">*</span>
                        </label>
                        <input type="password"
                               class="form-control"
                               id="password"
                               name="password"
                               placeholder="Enter your password"
                               required>
                    </div>

                    <!-- Submit button -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Login
                        </button>
                    </div>
                </form>

                <!-- Link to registration page -->
                <p class="text-center mt-3 mb-0">
                    Don't have an account?
                    <a href="<?php echo BASE_URL; ?>/register.php">Register here</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php
// Include the footer
require_once __DIR__ . '/../includes/footer.php';
?>
