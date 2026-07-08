<?php
/**
 * delete.php — Soft Delete Teacher (Admin Only)
 *
 * Deactivates a teacher account (sets is_active = 0).
 * Shows confirmation page first.
 */

$page_title = 'Delete Teacher';
require_once __DIR__ . '/../../includes/header.php';
require_role('admin');

$teacher_id = intval($_GET['id'] ?? 0);

$teacher = Database::fetch_one(
    "SELECT t.*, u.email FROM teachers t JOIN users u ON t.user_id = u.id WHERE t.id = ?",
    [$teacher_id]
);

if (!$teacher) {
    flash('error', 'Teacher not found.');
    redirect(BASE_URL . '/teachers/list.php');
}

// Handle POST — soft delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        flash('error', 'Invalid form submission.');
    } else {
        Database::query("UPDATE users SET is_active = 0 WHERE id = ?", [$teacher['user_id']]);
        flash('success', 'Teacher "' . $teacher['first_name'] . ' ' . $teacher['last_name'] . '" has been deactivated.');
    }
    redirect(BASE_URL . '/teachers/list.php');
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Confirm Deactivation</h5>
            </div>
            <div class="card-body">
                <p>Are you sure you want to deactivate this teacher?</p>
                <ul>
                    <li><strong>Name:</strong> <?php echo sanitize($teacher['first_name'] . ' ' . $teacher['last_name']); ?></li>
                    <li><strong>Employee Number:</strong> <?php echo sanitize($teacher['employee_number']); ?></li>
                    <li><strong>Email:</strong> <?php echo sanitize($teacher['email']); ?></li>
                    <li><strong>Department:</strong> <?php echo sanitize($teacher['department']); ?></li>
                </ul>
                <p class="text-muted">The account will be deactivated but records will be preserved.</p>

                <form method="POST" action="<?php echo BASE_URL; ?>/teachers/delete.php?id=<?php echo $teacher_id; ?>">
                    <?php csrf_field(); ?>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-1"></i>Yes, Deactivate</button>
                        <a href="<?php echo BASE_URL; ?>/teachers/list.php" class="btn btn-secondary"><i class="bi bi-x-circle me-1"></i>Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
