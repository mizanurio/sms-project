<?php
/**
 * delete.php — Drop Enrollment (Admin Only)
 *
 * Changes enrollment status to 'dropped' instead of deleting.
 * Shows a confirmation page before making the change.
 */

$page_title = 'Drop Enrollment';
require_once __DIR__ . '/../../includes/header.php';
require_role('admin');

$enrollment_id = intval($_GET['id'] ?? 0);

// Fetch enrollment with student and course info
$enrollment = Database::fetch_one(
    "SELECT e.*, s.first_name as s_first, s.last_name as s_last, s.student_number,
            c.course_code, c.course_name
     FROM enrollments e
     JOIN students s ON e.student_id = s.id
     JOIN courses c ON e.course_id = c.id
     WHERE e.id = ?",
    [$enrollment_id]
);

if (!$enrollment) {
    flash('error', 'Enrollment not found.');
    redirect(BASE_URL . '/enrollments/list.php');
}

// Handle POST — set status to dropped
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        flash('error', 'Invalid form submission.');
    } else {
        Database::query("UPDATE enrollments SET status = 'dropped' WHERE id = ?", [$enrollment_id]);
        flash('success', 'Enrollment has been dropped.');
    }
    redirect(BASE_URL . '/enrollments/list.php');
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-warning">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Confirm Drop Enrollment</h5>
            </div>
            <div class="card-body">
                <p>Are you sure you want to drop this enrollment?</p>
                <ul>
                    <li><strong>Student:</strong> <?php echo sanitize($enrollment['s_first'] . ' ' . $enrollment['s_last']); ?>
                        (<?php echo sanitize($enrollment['student_number']); ?>)</li>
                    <li><strong>Course:</strong> <?php echo sanitize($enrollment['course_code'] . ' — ' . $enrollment['course_name']); ?></li>
                    <li><strong>Enrolled On:</strong> <?php echo format_date($enrollment['enrollment_date']); ?></li>
                    <li><strong>Current Status:</strong> <?php echo ucfirst(sanitize($enrollment['status'])); ?></li>
                </ul>
                <p class="text-muted">The enrollment status will be changed to "dropped". Records will be preserved.</p>

                <form method="POST" action="<?php echo BASE_URL; ?>/enrollments/delete.php?id=<?php echo $enrollment_id; ?>">
                    <?php csrf_field(); ?>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning"><i class="bi bi-x-circle me-1"></i>Yes, Drop</button>
                        <a href="<?php echo BASE_URL; ?>/enrollments/list.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
