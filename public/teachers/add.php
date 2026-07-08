<?php
/**
 * add.php — Add New Teacher (Admin Only)
 *
 * Creates a user account (role=teacher) and teacher profile in one transaction.
 * Auto-generates employee number (EMP001, EMP002, etc.).
 */

$page_title = 'Add Teacher';
require_once __DIR__ . '/../../includes/header.php';
require_role('admin');

$form = [
    'username' => '', 'email' => '', 'first_name' => '', 'last_name' => '',
    'department' => '', 'phone' => '', 'hire_date' => date('Y-m-d')
];
$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $errors[] = 'Invalid form submission.';
    } else {
        $form['username']   = trim($_POST['username'] ?? '');
        $form['email']      = trim($_POST['email'] ?? '');
        $password           = $_POST['password'] ?? '';
        $confirm            = $_POST['confirm_password'] ?? '';
        $form['first_name'] = trim($_POST['first_name'] ?? '');
        $form['last_name']  = trim($_POST['last_name'] ?? '');
        $form['department'] = trim($_POST['department'] ?? '');
        $form['phone']      = trim($_POST['phone'] ?? '');
        $form['hire_date']  = trim($_POST['hire_date'] ?? '');

        // Validate
        if (empty($form['username'])) $errors[] = 'Username is required.';
        if (empty($form['email'])) $errors[] = 'Email is required.';
        elseif (!filter_var($form['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email.';
        if (empty($form['first_name'])) $errors[] = 'First name is required.';
        if (empty($form['last_name'])) $errors[] = 'Last name is required.';
        if (empty($form['department'])) $errors[] = 'Department is required.';
        if (empty($form['hire_date'])) $errors[] = 'Hire date is required.';
        if (empty($password)) $errors[] = 'Password is required.';
        elseif (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
        if ($password !== $confirm) $errors[] = 'Passwords do not match.';

        // Check duplicates
        if (empty($errors)) {
            if (Database::fetch_one("SELECT id FROM users WHERE email = ?", [$form['email']])) $errors[] = 'Email already exists.';
            if (Database::fetch_one("SELECT id FROM users WHERE username = ?", [$form['username']])) $errors[] = 'Username already taken.';
        }

        if (empty($errors)) {
            try {
                Database::begin_transaction();
                $hash = password_hash($password, PASSWORD_DEFAULT);
                Database::query("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, 'teacher')",
                    [$form['username'], $form['email'], $hash]);
                $user_id = Database::last_insert_id();

                // Generate employee number
                $last = Database::fetch_one("SELECT employee_number FROM teachers ORDER BY id DESC LIMIT 1");
                $num = $last ? intval(substr($last['employee_number'], 3)) + 1 : 1;
                $emp_number = 'EMP' . str_pad($num, 3, '0', STR_PAD_LEFT);

                Database::query(
                    "INSERT INTO teachers (user_id, employee_number, first_name, last_name, department, phone, hire_date)
                     VALUES (?, ?, ?, ?, ?, ?, ?)",
                    [$user_id, $emp_number, $form['first_name'], $form['last_name'],
                     $form['department'], $form['phone'] ?: null, $form['hire_date']]
                );
                Database::commit();
                flash('success', 'Teacher added successfully.');
                redirect(BASE_URL . '/teachers/list.php');
            } catch (Exception $e) {
                Database::rollback();
                $errors[] = 'Failed to add teacher. Please try again.';
            }
        }
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-plus-circle me-2"></i>Add Teacher</h1>
    <a href="<?php echo BASE_URL; ?>/teachers/list.php" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to List
    </a>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0 ps-3">
            <?php foreach ($errors as $e): ?><li><?php echo sanitize($e); ?></li><?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="POST">
            <?php csrf_field(); ?>

            <h5 class="mb-3">Account Information</h5>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="username" name="username"
                           value="<?php echo sanitize($form['username']); ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email"
                           value="<?php echo sanitize($form['email']); ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <div class="form-text">Min 8 characters.</div>
                </div>
                <div class="col-md-4">
                    <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
            </div>

            <hr>
            <h5 class="mb-3">Personal Information</h5>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="first_name" name="first_name"
                           value="<?php echo sanitize($form['first_name']); ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="last_name" name="last_name"
                           value="<?php echo sanitize($form['last_name']); ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="department" class="form-label">Department <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="department" name="department"
                           value="<?php echo sanitize($form['department']); ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="tel" class="form-control" id="phone" name="phone"
                           value="<?php echo sanitize($form['phone']); ?>">
                </div>
                <div class="col-md-4">
                    <label for="hire_date" class="form-label">Hire Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="hire_date" name="hire_date"
                           value="<?php echo sanitize($form['hire_date']); ?>" required>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Add Teacher</button>
                <a href="<?php echo BASE_URL; ?>/teachers/list.php" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
