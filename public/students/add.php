<?php
/**
 * students/add.php — Add New Student Form & Processing
 *
 * Allows admin users to create new student accounts and profiles.
 * Handles form validation, CSRF protection, and database transaction
 * to create both user and student records atomically.
 *
 * POST Parameters:
 *  - csrf_token: CSRF security token
 *  - username: Unique username
 *  - email: Valid email address
 *  - password: Password (minimum 8 characters)
 *  - confirm_password: Password confirmation
 *  - first_name: Student's first name
 *  - last_name: Student's last name
 *  - date_of_birth: Date of birth (YYYY-MM-DD format, optional)
 *  - gender: Gender (optional)
 *  - phone: Phone number (optional)
 *  - address: Street address (optional)
 */

// ============================================================
// SETUP & AUTHORIZATION
// ============================================================

// Set page title before including header
$page_title = 'Add New Student';

// Include header (which includes auth, db, functions, config, csrf)
require_once __DIR__ . '/../../includes/header.php';

// Require admin role for this page
require_role('admin');

// ============================================================
// INITIALIZE ERROR AND SUCCESS TRACKING
// ============================================================

// Array to hold validation errors
$errors = [];

// Array to preserve form data on validation failure
$form_data = [
    'username' => '',
    'email' => '',
    'first_name' => '',
    'last_name' => '',
    'date_of_birth' => '',
    'gender' => '',
    'phone' => '',
    'address' => '',
];

// ============================================================
// HANDLE FORM SUBMISSION (POST)
// ============================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token to prevent cross-site request forgery
    if (!csrf_verify()) {
        $errors[] = 'Invalid form submission. Please try again.';
    } else {
        // ========================================
        // COLLECT AND SANITIZE FORM INPUT
        // ========================================

        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $dob = trim($_POST['date_of_birth'] ?? '');
        $gender = trim($_POST['gender'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');

        // Preserve form data for display on error
        $form_data = compact('username', 'email', 'first_name', 'last_name', 'dob', 'gender', 'phone', 'address');

        // ========================================
        // VALIDATE REQUIRED FIELDS
        // ========================================

        if (empty($username)) {
            $errors[] = 'Username is required.';
        }

        if (empty($email)) {
            $errors[] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }

        if (empty($first_name)) {
            $errors[] = 'First name is required.';
        }

        if (empty($last_name)) {
            $errors[] = 'Last name is required.';
        }

        if (empty($password)) {
            $errors[] = 'Password is required.';
        } elseif (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long.';
        }

        if ($password !== $confirm) {
            $errors[] = 'Passwords do not match.';
        }

        // ========================================
        // CHECK FOR DUPLICATE EMAIL & USERNAME
        // ========================================

        if (empty($errors)) {
            // Check if email already exists
            $existing_email = Database::fetch_one(
                "SELECT id FROM users WHERE email = ?",
                [$email]
            );
            if ($existing_email) {
                $errors[] = 'Email address is already registered.';
            }

            // Check if username already exists
            $existing_username = Database::fetch_one(
                "SELECT id FROM users WHERE username = ?",
                [$username]
            );
            if ($existing_username) {
                $errors[] = 'Username is already taken.';
            }
        }

        // ========================================
        // IF VALIDATION PASSED, CREATE STUDENT
        // ========================================

        if (empty($errors)) {
            try {
                // Start a database transaction
                Database::begin_transaction();

                // Hash the password securely using bcrypt
                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                // Create the user account with 'student' role
                Database::query(
                    "INSERT INTO users (username, email, password_hash, role, is_active) VALUES (?, ?, ?, 'student', 1)",
                    [$username, $email, $password_hash]
                );
                $user_id = Database::last_insert_id();

                // Generate unique student number (STU + 3-digit number)
                $last_student = Database::fetch_one(
                    "SELECT student_number FROM students ORDER BY id DESC LIMIT 1"
                );
                if ($last_student) {
                    // Extract numeric part and increment
                    $last_num = intval(substr($last_student['student_number'], 3));
                    $new_num = $last_num + 1;
                } else {
                    // First student
                    $new_num = 1;
                }
                $student_number = 'STU' . str_pad($new_num, 3, '0', STR_PAD_LEFT);

                // Create the student profile record
                Database::query(
                    "INSERT INTO students (user_id, student_number, first_name, last_name, date_of_birth, gender, phone, address, enrollment_year) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
                    [
                        $user_id,
                        $student_number,
                        $first_name,
                        $last_name,
                        !empty($dob) ? $dob : null,
                        !empty($gender) ? $gender : null,
                        !empty($phone) ? $phone : null,
                        !empty($address) ? $address : null,
                        date('Y')  // Current year for enrollment
                    ]
                );

                // Commit the transaction
                Database::commit();

                // Flash success message and redirect
                flash('success', "Student '{$first_name} {$last_name}' has been added successfully with student number {$student_number}.");
                redirect(BASE_URL . '/students/list.php');
                exit;

            } catch (Exception $e) {
                // Rollback transaction on error
                Database::rollback();

                // Log the error for debugging
                error_log('Student creation failed: ' . $e->getMessage());

                // Show generic error to user
                $errors[] = 'Failed to create student account. Please try again.';
            }
        }
    }
}

?>

<!-- ============================================================ -->
<!-- PAGE HEADER                                                  -->
<!-- ============================================================ -->
<div class="row mb-4">
    <div class="col-md-8">
        <h1 class="page-heading">Add New Student</h1>
        <p class="text-muted">Create a new student account and profile</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="<?php echo BASE_URL; ?>/students/list.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Students
        </a>
    </div>
</div>

<!-- ============================================================ -->
<!-- ERROR MESSAGES                                               -->
<!-- ============================================================ -->
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h5 class="alert-heading">
            <i class="bi bi-exclamation-circle me-2"></i>Validation Errors
        </h5>
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?php echo sanitize($error); ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- ============================================================ -->
<!-- STUDENT FORM                                                 -->
<!-- ============================================================ -->
<div class="card">
    <div class="card-body p-5">
        <form method="POST" action="<?php echo BASE_URL; ?>/students/add.php" novalidate>
            <!-- CSRF Token -->
            <?php csrf_field(); ?>

            <!-- ============================================ -->
            <!-- ACCOUNT INFORMATION SECTION                 -->
            <!-- ============================================ -->
            <h5 class="mb-4 text-primary">
                <i class="bi bi-shield-lock me-2"></i>Account Information
            </h5>

            <!-- Row 1: Username and Email -->
            <div class="row mb-3">
                <!-- Username -->
                <div class="col-md-6">
                    <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        class="form-control"
                        placeholder="Enter unique username"
                        value="<?php echo sanitize($form_data['username']); ?>"
                        required
                    >
                    <small class="text-muted">Unique identifier for login</small>
                </div>

                <!-- Email -->
                <div class="col-md-6">
                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        placeholder="Enter email address"
                        value="<?php echo sanitize($form_data['email']); ?>"
                        required
                    >
                    <small class="text-muted">Must be a valid email address</small>
                </div>
            </div>

            <!-- Row 2: Password and Confirm Password -->
            <div class="row mb-4">
                <!-- Password -->
                <div class="col-md-6">
                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        placeholder="Enter password (min 8 characters)"
                        required
                    >
                    <small class="text-muted">Minimum 8 characters required</small>
                </div>

                <!-- Confirm Password -->
                <div class="col-md-6">
                    <label for="confirm-password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                    <input
                        type="password"
                        id="confirm-password"
                        name="confirm_password"
                        class="form-control"
                        placeholder="Re-enter password"
                        required
                    >
                </div>
            </div>

            <!-- ============================================ -->
            <!-- PERSONAL INFORMATION SECTION                -->
            <!-- ============================================ -->
            <h5 class="mb-4 text-primary">
                <i class="bi bi-person me-2"></i>Personal Information
            </h5>

            <!-- Row 3: First Name and Last Name -->
            <div class="row mb-3">
                <!-- First Name -->
                <div class="col-md-6">
                    <label for="first-name" class="form-label">First Name <span class="text-danger">*</span></label>
                    <input
                        type="text"
                        id="first-name"
                        name="first_name"
                        class="form-control"
                        placeholder="Enter first name"
                        value="<?php echo sanitize($form_data['first_name']); ?>"
                        required
                    >
                </div>

                <!-- Last Name -->
                <div class="col-md-6">
                    <label for="last-name" class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input
                        type="text"
                        id="last-name"
                        name="last_name"
                        class="form-control"
                        placeholder="Enter last name"
                        value="<?php echo sanitize($form_data['last_name']); ?>"
                        required
                    >
                </div>
            </div>

            <!-- Row 4: Date of Birth and Gender -->
            <div class="row mb-3">
                <!-- Date of Birth -->
                <div class="col-md-6">
                    <label for="date-of-birth" class="form-label">Date of Birth</label>
                    <input
                        type="date"
                        id="date-of-birth"
                        name="date_of_birth"
                        class="form-control"
                        value="<?php echo sanitize($form_data['dob']); ?>"
                    >
                    <small class="text-muted">Optional - Format: YYYY-MM-DD</small>
                </div>

                <!-- Gender -->
                <div class="col-md-6">
                    <label for="gender" class="form-label">Gender</label>
                    <select id="gender" name="gender" class="form-select">
                        <option value="">Select Gender...</option>
                        <option value="Male" <?php echo $form_data['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo $form_data['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo $form_data['gender'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
            </div>

            <!-- Row 5: Phone and Address -->
            <div class="row mb-4">
                <!-- Phone -->
                <div class="col-md-6">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        class="form-control"
                        placeholder="Enter phone number"
                        value="<?php echo sanitize($form_data['phone']); ?>"
                    >
                    <small class="text-muted">Optional</small>
                </div>

                <!-- Address -->
                <div class="col-md-6">
                    <label for="address" class="form-label">Street Address</label>
                    <input
                        type="text"
                        id="address"
                        name="address"
                        class="form-control"
                        placeholder="Enter street address"
                        value="<?php echo sanitize($form_data['address']); ?>"
                    >
                    <small class="text-muted">Optional</small>
                </div>
            </div>

            <!-- ============================================ -->
            <!-- FORM ACTIONS                                -->
            <!-- ============================================ -->
            <div class="d-flex gap-2 justify-content-end mt-5">
                <!-- Cancel Button -->
                <a href="<?php echo BASE_URL; ?>/students/list.php" class="btn btn-secondary">
                    <i class="bi bi-x-circle me-2"></i>Cancel
                </a>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle me-2"></i>Create Student
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================ -->
<!-- INFO PANEL                                                   -->
<!-- ============================================================ -->
<div class="mt-4">
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Note:</strong> A student number will be automatically generated when the student account is created.
        The account will be immediately active and the student can log in using the username and password you set.
    </div>
</div>

<?php
// ============================================================
// INCLUDE FOOTER
// ============================================================
require_once __DIR__ . '/../../includes/footer.php';
?>
