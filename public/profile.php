<?php
/**
 * profile.php — User Profile (All Roles)
 *
 * Allows users to view and edit their own profile.
 * Supports editing contact details and changing password.
 * Shows different profile fields based on role.
 */

$page_title = 'My Profile';
require_once __DIR__ . '/../includes/header.php';
require_login();

$user = current_user();
$errors = [];
$password_errors = [];

// Fetch profile data based on role
if ($user['role'] === 'student') {
    $profile = Database::fetch_one("SELECT * FROM students WHERE user_id = ?", [$user['id']]);
} elseif ($user['role'] === 'teacher') {
    $profile = Database::fetch_one("SELECT * FROM teachers WHERE user_id = ?", [$user['id']]);
} else {
    $profile = null; // Admin has no separate profile table
}

// ============================================================
// Handle profile update
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    if (!csrf_verify()) {
        $errors[] = 'Invalid form submission.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        // Validate email
        if (empty($email)) {
            $errors[] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email address.';
        } else {
            // Check uniqueness
            $existing = Database::fetch_one("SELECT id FROM users WHERE email = ? AND id != ?", [$email, $user['id']]);
            if ($existing) $errors[] = 'This email is already in use.';
        }

        if (empty($errors)) {
            // Update users table
            Database::query("UPDATE users SET email = ? WHERE id = ?", [$email, $user['id']]);

            // Update profile table
            if ($user['role'] === 'student' && $profile) {
                $address = trim($_POST['address'] ?? '');
                Database::query("UPDATE students SET phone = ?, address = ? WHERE user_id = ?",
                    [$phone ?: null, $address ?: null, $user['id']]);
            } elseif ($user['role'] === 'teacher' && $profile) {
                Database::query("UPDATE teachers SET phone = ? WHERE user_id = ?",
                    [$phone ?: null, $user['id']]);
            }

            // Refresh data
            $_SESSION['email'] = $email;
            flash('success', 'Profile updated successfully.');
            redirect(BASE_URL . '/profile.php');
        }
    }
}

// ============================================================
// Handle password change
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    if (!csrf_verify()) {
        $password_errors[] = 'Invalid form submission.';
    } else {
        $current_pass = $_POST['current_password'] ?? '';
        $new_pass = $_POST['new_password'] ?? '';
        $confirm_pass = $_POST['confirm_password'] ?? '';

        // Verify current password
        $user_data = Database::fetch_one("SELECT password_hash FROM users WHERE id = ?", [$user['id']]);
        if (!password_verify($current_pass, $user_data['password_hash'])) {
            $password_errors[] = 'Current password is incorrect.';
        }

        // Validate new password
        if (empty($new_pass)) {
            $password_errors[] = 'New password is required.';
        } elseif (strlen($new_pass) < 8) {
            $password_errors[] = 'Password must be at least 8 characters.';
        } elseif (!preg_match('/[A-Z]/', $new_pass)) {
            $password_errors[] = 'Password must contain at least one uppercase letter.';
        } elseif (!preg_match('/[0-9]/', $new_pass)) {
            $password_errors[] = 'Password must contain at least one number.';
        }

        if ($new_pass !== $confirm_pass) {
            $password_errors[] = 'New passwords do not match.';
        }

        if (empty($password_errors)) {
            $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
            Database::query("UPDATE users SET password_hash = ? WHERE id = ?", [$new_hash, $user['id']]);
            flash('success', 'Password changed successfully.');
            redirect(BASE_URL . '/profile.php');
        }
    }
}

// Refresh user and profile data for display
$user = current_user();
if ($user['role'] === 'student') {
    $profile = Database::fetch_one("SELECT * FROM students WHERE user_id = ?", [$user['id']]);
} elseif ($user['role'] === 'teacher') {
    $profile = Database::fetch_one("SELECT * FROM teachers WHERE user_id = ?", [$user['id']]);
}
$display_name = get_display_name($user);
?>

<h1 class="h3 mb-4"><i class="bi bi-person-circle me-2"></i>My Profile</h1>

<div class="row g-4">
    <!-- Profile Information -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-white"><h5 class="mb-0">Profile Information</h5></div>
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            <?php foreach ($errors as $e): ?><li><?php echo sanitize($e); ?></li><?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <?php csrf_field(); ?>

                    <!-- Account Info (read-only) -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" value="<?php echo sanitize($user['username']); ?>" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Role</label>
                            <input type="text" class="form-control" value="<?php echo ucfirst(sanitize($user['role'])); ?>" disabled>
                        </div>
                    </div>

                    <!-- Editable fields -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email"
                               value="<?php echo sanitize($user['email']); ?>" required>
                    </div>

                    <?php if ($profile): ?>
                        <!-- Role-specific read-only info -->
                        <div class="row mb-3">
                            <?php if ($user['role'] === 'student'): ?>
                                <div class="col-md-4">
                                    <label class="form-label">Student Number</label>
                                    <input type="text" class="form-control" value="<?php echo sanitize($profile['student_number']); ?>" disabled>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" value="<?php echo sanitize($profile['first_name'] . ' ' . $profile['last_name']); ?>" disabled>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Enrollment Year</label>
                                    <input type="text" class="form-control" value="<?php echo sanitize($profile['enrollment_year']); ?>" disabled>
                                </div>
                            <?php elseif ($user['role'] === 'teacher'): ?>
                                <div class="col-md-4">
                                    <label class="form-label">Employee Number</label>
                                    <input type="text" class="form-control" value="<?php echo sanitize($profile['employee_number']); ?>" disabled>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" value="<?php echo sanitize($profile['first_name'] . ' ' . $profile['last_name']); ?>" disabled>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Department</label>
                                    <input type="text" class="form-control" value="<?php echo sanitize($profile['department']); ?>" disabled>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Editable contact fields -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="phone" name="phone"
                                       value="<?php echo sanitize($profile['phone']); ?>">
                            </div>
                            <?php if ($user['role'] === 'student'): ?>
                                <div class="col-md-6">
                                    <label for="address" class="form-label">Address</label>
                                    <input type="text" class="form-control" id="address" name="address"
                                           value="<?php echo sanitize($profile['address']); ?>">
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <button type="submit" name="update_profile" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Save Changes
                    </button>
                </form>
            </div>
        </div>

        <!-- Change Password -->
        <div class="card">
            <div class="card-header bg-white"><h5 class="mb-0">Change Password</h5></div>
            <div class="card-body">
                <?php if (!empty($password_errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            <?php foreach ($password_errors as $e): ?><li><?php echo sanitize($e); ?></li><?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <?php csrf_field(); ?>
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="new_password" class="form-label">New Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                            <div class="form-text">Min 8 chars, 1 uppercase, 1 number.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="confirm_password" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                    </div>
                    <button type="submit" name="change_password" class="btn btn-warning">
                        <i class="bi bi-key me-1"></i>Change Password
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Profile Summary Sidebar -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <i class="bi bi-person-circle text-primary" style="font-size: 5rem;"></i>
                <h4 class="mt-3"><?php echo sanitize($display_name); ?></h4>
                <span class="badge bg-primary fs-6"><?php echo ucfirst(sanitize($user['role'])); ?></span>
                <hr>
                <p class="text-muted mb-1"><i class="bi bi-envelope me-1"></i><?php echo sanitize($user['email']); ?></p>
                <?php if ($profile && !empty($profile['phone'])): ?>
                    <p class="text-muted mb-1"><i class="bi bi-telephone me-1"></i><?php echo sanitize($profile['phone']); ?></p>
                <?php endif; ?>
                <p class="text-muted mb-0">
                    <i class="bi bi-calendar me-1"></i>Joined <?php echo format_date($user['created_at']); ?>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
