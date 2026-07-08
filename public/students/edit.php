<?php
/**
 * edit.php — Edit Student Information
 *
 * Allows admins to edit a student's personal information (name, email, DOB, phone, address).
 * Updates both the students and users tables atomically.
 * Validates email uniqueness and prevents duplicate submissions via CSRF tokens.
 */

$page_title = 'Edit Student';
require_once __DIR__ . '/../../includes/header.php';
require_role('admin');

// ============================================================================
// Get student ID from URL parameter
// ============================================================================
$student_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($student_id <= 0) {
    flash('error', 'Invalid student ID.');
    redirect(BASE_URL . '/students/list.php');
}

// ============================================================================
// Fetch student and user data
// ============================================================================
$student = Database::fetch_one(
    "SELECT s.*, u.email, u.username FROM students s
     JOIN users u ON s.user_id = u.id
     WHERE s.id = ?",
    [$student_id]
);

if (!$student) {
    flash('error', 'Student not found.');
    redirect(BASE_URL . '/students/list.php');
}

// ============================================================================
// Initialize form data (use POST data on error, or student data initially)
// ============================================================================
$form_data = [
    'first_name'    => $_POST['first_name'] ?? $student['first_name'] ?? '',
    'last_name'     => $_POST['last_name'] ?? $student['last_name'] ?? '',
    'email'         => $_POST['email'] ?? $student['email'] ?? '',
    'date_of_birth' => $_POST['date_of_birth'] ?? $student['date_of_birth'] ?? '',
    'gender'        => $_POST['gender'] ?? $student['gender'] ?? '',
    'phone'         => $_POST['phone'] ?? $student['phone'] ?? '',
    'address'       => $_POST['address'] ?? $student['address'] ?? '',
];

// ============================================================================
// Initialize error array
// ============================================================================
$errors = [];

// ============================================================================
// Handle POST request (form submission)
// ============================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Verify CSRF token
    if (!csrf_verify()) {
        flash('error', 'Invalid form submission. Please try again.');
        redirect(BASE_URL . '/students/edit.php?id=' . $student_id);
    }

    // ========================================================================
    // Validate form fields
    // ========================================================================
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $date_of_birth = trim($_POST['date_of_birth'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    // Validate first name
    if (empty($first_name)) {
        $errors['first_name'] = 'First name is required.';
    } elseif (strlen($first_name) > 50) {
        $errors['first_name'] = 'First name must not exceed 50 characters.';
    }

    // Validate last name
    if (empty($last_name)) {
        $errors['last_name'] = 'Last name is required.';
    } elseif (strlen($last_name) > 50) {
        $errors['last_name'] = 'Last name must not exceed 50 characters.';
    }

    // Validate email
    if (empty($email)) {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    } else {
        // Check email uniqueness (exclude current user)
        $email_exists = Database::fetch_one(
            "SELECT id FROM users WHERE email = ? AND id != ?",
            [$email, $student['user_id']]
        );
        if ($email_exists) {
            $errors['email'] = 'This email address is already in use.';
        }
    }

    // Validate date of birth
    if (empty($date_of_birth)) {
        $errors['date_of_birth'] = 'Date of birth is required.';
    } else {
        $dob = strtotime($date_of_birth);
        if ($dob === false) {
            $errors['date_of_birth'] = 'Invalid date format.';
        } else {
            $age = intval((time() - $dob) / (365.25 * 24 * 3600));
            if ($age < 15 || $age > 80) {
                $errors['date_of_birth'] = 'Student age must be between 15 and 80 years.';
            }
        }
    }

    // Validate gender
    $valid_genders = ['Male', 'Female', 'Other'];
    if (empty($gender)) {
        $errors['gender'] = 'Gender is required.';
    } elseif (!in_array($gender, $valid_genders, true)) {
        $errors['gender'] = 'Invalid gender selection.';
    }

    // Validate phone (optional but format check if provided)
    if (!empty($phone) && !preg_match('/^[\d\-\s\+\(\)]{7,20}$/', $phone)) {
        $errors['phone'] = 'Phone number format is invalid.';
    }

    // Validate address
    if (strlen($address) > 255) {
        $errors['address'] = 'Address must not exceed 255 characters.';
    }

    // ========================================================================
    // If no errors, update the database
    // ========================================================================
    if (empty($errors)) {
        try {
            // Start transaction
            $db = Database::get_pdo();
            $db->beginTransaction();

            // Update students table
            Database::query(
                "UPDATE students
                 SET first_name = ?, last_name = ?, date_of_birth = ?, gender = ?, phone = ?, address = ?
                 WHERE id = ?",
                [$first_name, $last_name, $date_of_birth, $gender, $phone, $address, $student_id]
            );

            // Update users email
            Database::query(
                "UPDATE users SET email = ? WHERE id = ?",
                [$email, $student['user_id']]
            );

            // Commit transaction
            $db->commit();

            // Flash success message and redirect
            flash('success', 'Student information updated successfully.');
            redirect(BASE_URL . '/students/view.php?id=' . $student_id);

        } catch (Exception $e) {
            // Rollback on error
            if (isset($db)) {
                $db->rollBack();
            }
            $errors['form'] = 'An error occurred while updating the student. Please try again.';
        }
    }

    // If there are errors, preserve form data for re-display
    $form_data = [
        'first_name'    => $first_name,
        'last_name'     => $last_name,
        'email'         => $email,
        'date_of_birth' => $date_of_birth,
        'gender'        => $gender,
        'phone'         => $phone,
        'address'       => $address,
    ];
}

?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Page Header -->
            <div class="mb-4">
                <h1 class="h3 mb-2">Edit Student</h1>
                <p class="text-muted">
                    Update student information for: <strong><?php echo sanitize($student['first_name'] . ' ' . $student['last_name']); ?></strong>
                </p>
            </div>

            <!-- General Error Message -->
            <?php if (!empty($errors['form'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <?php echo sanitize($errors['form']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Edit Form Card -->
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="<?php echo BASE_URL; ?>/students/edit.php?id=<?php echo $student_id; ?>" novalidate>

                        <!-- CSRF Token -->
                        <?php csrf_field(); ?>

                        <!-- First Name -->
                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                class="form-control <?php echo !empty($errors['first_name']) ? 'is-invalid' : ''; ?>"
                                id="first_name"
                                name="first_name"
                                value="<?php echo sanitize($form_data['first_name']); ?>"
                                maxlength="50"
                                required
                            >
                            <?php if (!empty($errors['first_name'])): ?>
                                <div class="invalid-feedback d-block">
                                    <i class="bi bi-exclamation-triangle me-1"></i><?php echo sanitize($errors['first_name']); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Last Name -->
                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                class="form-control <?php echo !empty($errors['last_name']) ? 'is-invalid' : ''; ?>"
                                id="last_name"
                                name="last_name"
                                value="<?php echo sanitize($form_data['last_name']); ?>"
                                maxlength="50"
                                required
                            >
                            <?php if (!empty($errors['last_name'])): ?>
                                <div class="invalid-feedback d-block">
                                    <i class="bi bi-exclamation-triangle me-1"></i><?php echo sanitize($errors['last_name']); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input
                                type="email"
                                class="form-control <?php echo !empty($errors['email']) ? 'is-invalid' : ''; ?>"
                                id="email"
                                name="email"
                                value="<?php echo sanitize($form_data['email']); ?>"
                                required
                            >
                            <?php if (!empty($errors['email'])): ?>
                                <div class="invalid-feedback d-block">
                                    <i class="bi bi-exclamation-triangle me-1"></i><?php echo sanitize($errors['email']); ?>
                                </div>
                            <?php endif; ?>
                            <small class="text-muted d-block mt-1">Note: Username is not editable and remains <strong><?php echo sanitize($student['username']); ?></strong></small>
                        </div>

                        <!-- Date of Birth -->
                        <div class="mb-3">
                            <label for="date_of_birth" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                            <input
                                type="date"
                                class="form-control <?php echo !empty($errors['date_of_birth']) ? 'is-invalid' : ''; ?>"
                                id="date_of_birth"
                                name="date_of_birth"
                                value="<?php echo sanitize($form_data['date_of_birth']); ?>"
                                required
                            >
                            <?php if (!empty($errors['date_of_birth'])): ?>
                                <div class="invalid-feedback d-block">
                                    <i class="bi bi-exclamation-triangle me-1"></i><?php echo sanitize($errors['date_of_birth']); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Gender -->
                        <div class="mb-3">
                            <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                            <select
                                class="form-select <?php echo !empty($errors['gender']) ? 'is-invalid' : ''; ?>"
                                id="gender"
                                name="gender"
                                required
                            >
                                <option value="">-- Select Gender --</option>
                                <option value="Male" <?php echo $form_data['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo $form_data['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                                <option value="Other" <?php echo $form_data['gender'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                            <?php if (!empty($errors['gender'])): ?>
                                <div class="invalid-feedback d-block">
                                    <i class="bi bi-exclamation-triangle me-1"></i><?php echo sanitize($errors['gender']); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Phone -->
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input
                                type="tel"
                                class="form-control <?php echo !empty($errors['phone']) ? 'is-invalid' : ''; ?>"
                                id="phone"
                                name="phone"
                                value="<?php echo sanitize($form_data['phone']); ?>"
                                placeholder="+1 (555) 123-4567"
                            >
                            <?php if (!empty($errors['phone'])): ?>
                                <div class="invalid-feedback d-block">
                                    <i class="bi bi-exclamation-triangle me-1"></i><?php echo sanitize($errors['phone']); ?>
                                </div>
                            <?php endif; ?>
                            <small class="text-muted d-block mt-1">Optional field</small>
                        </div>

                        <!-- Address -->
                        <div class="mb-4">
                            <label for="address" class="form-label">Address</label>
                            <textarea
                                class="form-control <?php echo !empty($errors['address']) ? 'is-invalid' : ''; ?>"
                                id="address"
                                name="address"
                                rows="3"
                                maxlength="255"
                                placeholder="Street, City, State, ZIP"
                            ><?php echo sanitize($form_data['address']); ?></textarea>
                            <?php if (!empty($errors['address'])): ?>
                                <div class="invalid-feedback d-block">
                                    <i class="bi bi-exclamation-triangle me-1"></i><?php echo sanitize($errors['address']); ?>
                                </div>
                            <?php endif; ?>
                            <small class="text-muted d-block mt-1">Optional field (max 255 characters)</small>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2 justify-content-between">
                            <a href="<?php echo BASE_URL; ?>/students/view.php?id=<?php echo $student_id; ?>" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Additional Info Card -->
            <div class="card mt-4 bg-light">
                <div class="card-body">
                    <p class="mb-0 text-muted">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Note:</strong> Student number and username cannot be changed.
                        To reset a password, use the user management tools.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
