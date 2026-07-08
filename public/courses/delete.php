<?php
/**
 * delete.php — Delete Course (Admin Only)
 *
 * Permanently deletes a course. Shows confirmation page first.
 * Warning: CASCADE will also delete related enrollments, grades, and attendance.
 */

$page_title = 'Delete Course';
require_once __DIR__ . '/../../includes/header.php';
require_role('admin');

$course_id = intval($_GET['id'] ?? 0);

// Fetch course details
$course = Database::fetch_one("SELECT * FROM courses WHERE id = ?", [$course_id]);
if (!$course) {
    flash('error', 'Course not found.');
    redirect(BASE_URL . '/courses/list.php');
}

// Count enrolled students for the warning
$enrollment_count = Database::fetch_one(
    "SELECT COUNT(*) as count FROM enrollments WHERE course_id = ?", [$course_id]
)['count'];

// Handle POST — actual deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        flash('error', 'Invalid form submission.');
    } else {
        Database::query("DELETE FROM courses WHERE id = ?", [$course_id]);
        flash('success', 'Course "' . $course['course_name'] . '" has been deleted.');
    }
    redirect(BASE_URL . '/courses/list.php');
}
?>

<!-- Confirmation Page -->
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Confirm Deletion</h5>
            </div>
            <div class="card-body">
                <p>Are you sure you want to delete this course?</p>
                <ul>
                    <li><strong>Code:</strong> <?php echo sanitize($course['course_code']); ?></li>
                    <li><strong>Name:</strong> <?php echo sanitize($course['course_name']); ?></li>
                    <li><strong>Enrolled Students:</strong> <?php echo $enrollment_count; ?></li>
                </ul>
                <?php if ($enrollment_count > 0): ?>
                    <div class="alert alert-warning mb-3">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        <strong>Warning:</strong> Deleting this course will also remove all
                        <?php echo $enrollment_count; ?> enrollment(s) and their associated grades and attendance records.
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo BASE_URL; ?>/courses/delete.php?id=<?php echo $course_id; ?>">
                    <?php csrf_field(); ?>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i>Yes, Delete
                        </button>
                        <a href="<?php echo BASE_URL; ?>/courses/list.php" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-1"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
