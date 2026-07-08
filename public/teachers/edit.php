<?php
/**
 * edit.php — Edit Teacher (Admin Only)
 *
 * Updates teacher profile and user email in a transaction.
 */

$page_title = 'Edit Teacher';
require_once __DIR__ . '/../../includes/header.php';
require_role('admin');

$teacher_id = intval($_GET['id'] ?? 0);

$teacher = Database::fetch_one(
    "SELECT t.*, u.email, u.username FROM teachers t JOIN users u ON t.user_id = u.id WHERE t.id = ?",
    [$teacher_id]
);

if (!$teacher) {
    flash('error', 'Teacher not found.');
    redirect(BASE_URL . '/teachers/list.php');
}

$form = [
    'first_name' => $teacher['first_name'], 'last_name' => $teacher['last_name'],
    'email' => $teacher['email'], 'department' => $teacher['department'],
    'phone' => $teacher['phone'], 'hire_date' => $teacher['hire_date'],
];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $errors[] = 'Invalid form submission.';
    } else {
        $form['first_name'] = trim($_POST['first_name'] ?? '');
        $form['last_name']  = trim($_POST['last_name'] ?? '');
        $form['email']      = trim($_POST['email'] ?? '');
        $form['department'] = trim($_POST['department'] ?? '');
        $form['phone']      = trim($_POST['phone'] ?? '');
        $form['hire_date']  = trim($_POST['hire_date'] ?? '');

        if (empty($form['first_name'])) $errors[] = 'First name is required.';
        if (empty($form['last_name'])) $errors[] = 'Last name is required.';
        if (empty($form['email'])) $errors[] = 'Email is required.';
        elseif (!filter_var($form['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email.';
        if (empty($form['department'])) $errors[] = 'Department is required.';
        if (empty($form['hire_date'])) $errors[] = 'Hire date is required.';

        // Check email uniqueness
        if (empty($errors)) {
            $existing = Database::fetch_one("SELECT id FROM users WHERE email = ? AND id != ?", [$form['email'], $teacher['user_id']]);
            if ($existing) $errors[] = 'This email is already in use.';
        }

        if (empty($errors)) {
            try {
                Database::begin_transaction();
                Database::query("UPDATE teachers SET first_name = ?, last_name = ?, department = ?, phone = ?, hire_date = ? WHERE id = ?",
                    [$form['first_name'], $form['last_name'], $form['department'], $form['phone'] ?: null, $form['hire_date'], $teacher_id]);
                Database::query("UPDATE users SET email = ? WHERE id = ?", [$form['email'], $teacher['user_id']]);
                Database::commit();
                flash('success', 'Teacher updated successfully.');
                redirect(BASE_URL . '/teachers/view.php?id=' . $teacher_id);
            } catch (Exception $e) {
                Database::rollback();
                $errors[] = 'Failed to update teacher.';
            }
        }
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-pencil me-2"></i>Edit Teacher</h1>
    <a href="<?php echo BASE_URL; ?>/teachers/list.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger"><ul class="mb-0 ps-3"><?php foreach ($errors as $e): ?><li><?php echo sanitize($e); ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="POST">
            <?php csrf_field(); ?>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Employee Number</label>
                    <input type="text" class="form-control" value="<?php echo sanitize($teacher['employee_number']); ?>" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Username</label>
                    <input type="text" class="form-control" value="<?php echo sanitize($teacher['username']); ?>" disabled>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo sanitize($form['first_name']); ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo sanitize($form['last_name']); ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo sanitize($form['email']); ?>" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="department" class="form-label">Department <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="department" name="department" value="<?php echo sanitize($form['department']); ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo sanitize($form['phone']); ?>">
                </div>
                <div class="col-md-4">
                    <label for="hire_date" class="form-label">Hire Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="hire_date" name="hire_date" value="<?php echo sanitize($form['hire_date']); ?>" required>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Save Changes</button>
                <a href="<?php echo BASE_URL; ?>/teachers/view.php?id=<?php echo $teacher_id; ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
