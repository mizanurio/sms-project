<?php
/**
 * register.php — Student Registration Page
 *
 * Allows new students to create an account.
 * Only creates student accounts — admin creates teachers and other admins manually.
 */

$page_title = 'Register';
require_once __DIR__ . '/../includes/header.php';

// If already logged in, go to dashboard
if (is_logged_in()) {
    redirect(BASE_URL . '/dashboard.php');
}

// Initialise form data and errors
$form_data = [
    'username'   => '',
    'email'      => '',
    'first_name' => '',
    'last_name'  => '',
    'date_of_birth' => '',
    'gender'     => '',
    'phone'      => '',
    'address'    => '',
];
$errors = [];

// ============================================================
// Handle form submission
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!csrf_verify()) {
        $errors[] = 'Invalid form submission. Please try again.';
    } else {
        // Collect and trim form data
        $form_data['username']      = trim($_POST['username'] ?? '');
        $form_data['email']         = trim($_POST['email'] ?? '');
        $form_data['first_name']    = trim($_POST['first_name'] ?? '');
        $form_data['last_name']     = trim($_POST['last_name'] ?? '');
        $form_data['date_of_birth'] = trim($_POST['date_of_birth'] ?? '');
        $form_data['gender']        = trim($_POST['gender'] ?? '');
        $form_data['phone']         = trim($_POST['phone'] ?? '');
        $form_data['address']       = trim($_POST['address'] ?? '');
        $password                   = $_POST['password'] ?? '';
        $confirm_password           = $_POST['confirm_password'] ?? '';

        // --- Validation ---

        if (empty($form_data['username'])) {
            $errors[] = 'Username is required.';
        } elseif (strlen($form_data['username']) < 3) {
            $errors[] = 'Username must be at least 3 characters.';
        }

        if (empty($form_data['email'])) {
            $errors[] = 'Email address is required.';
        } elseif (!filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }

        if (empty($form_data['first_name'])) {
            $errors[] = 'First name is required.';
        }

        if (empty($form_data['last_name'])) {
            $errors[] = 'Last name is required.';
        }

        // Password strength check: min 8 chars, 1 uppercase, 1 number
        if (empty($password)) {
            $errors[] = 'Password is required.';
        } elseif (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long.';
        } elseif (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter.';
        } elseif (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number.';
        }

        if ($password !== $confirm_password) {
            $errors[] = 'Passwords do not match.';
        }

        // Attempt registration if no errors
        if (empty($errors)) {
            $form_data['password'] = $password;
            $result = register_student($form_data);

            if ($result['success']) {
                flash('success', $result['message']);
                redirect(BASE_URL . '/index.php');
            } else {
                $errors[] = $result['message'];
            }
        }
    }
}
?>

<!-- ============================================================ -->
<!-- REGISTRATION FORM                                             -->
<!-- ============================================================ -->
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm mt-4">
            <div class="card-body p-4">
                <h2 class="card-title text-center mb-4">
                    <i class="bi bi-person-plus me-2"></i>Student Registration
                </h2>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo sanitize($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo BASE_URL; ?>/register.php">
                    <?php csrf_field(); ?>

                    <!-- Row 1: Username and Email -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="username" class="form-label">
                                Username <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="username" name="username"
                                   value="<?php echo sanitize($form_data['username']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">
                                Email Address <span class="text-danger">*</span>
                            </label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="<?php echo sanitize($form_data['email']); ?>" required>
                        </div>
                    </div>

                    <!-- Row 2: First Name and Last Name -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label">
                                First Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="first_name" name="first_name"
                                   value="<?php echo sanitize($form_data['first_name']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label">
                                Last Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="last_name" name="last_name"
                                   value="<?php echo sanitize($form_data['last_name']); ?>" required>
                        </div>
                    </div>

                    <!-- Row 3: Date of Birth and Gender -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                                   value="<?php echo sanitize($form_data['date_of_birth']); ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select" id="gender" name="gender">
                                <option value="">-- Select --</option>
                                <option value="male" <?php echo ($form_data['gender'] === 'male') ? 'selected' : ''; ?>>Male</option>
                                <option value="female" <?php echo ($form_data['gender'] === 'female') ? 'selected' : ''; ?>>Female</option>
                                <option value="other" <?php echo ($form_data['gender'] === 'other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 4: Phone -->
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="phone" name="phone"
                               value="<?php echo sanitize($form_data['phone']); ?>">
                    </div>

                    <!-- Row 5: Address -->
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="2"><?php echo sanitize($form_data['address']); ?></textarea>
                    </div>

                    <!-- Row 6: Password -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="password" class="form-label">
                                Password <span class="text-danger">*</span>
                            </label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="form-text">Min 8 characters, 1 uppercase, 1 number.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="confirm_password" class="form-label">
                                Confirm Password <span class="text-danger">*</span>
                            </label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-person-plus me-1"></i>Register
                        </button>
                    </div>
                </form>

                <p class="text-center mt-3 mb-0">
                    Already have an account?
                    <a href="<?php echo BASE_URL; ?>/index.php">Login here</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
