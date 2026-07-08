<?php
/**
 * delete.php — Soft Delete Student
 *
 * Deactivates a student account (sets is_active = 0).
 * Does NOT permanently remove data from the database.
 * Implements a two-step process: confirmation page (GET) and soft delete execution (POST).
 */

$page_title = 'Delete Student';
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
// Fetch the student to confirm deletion
// ============================================================================
$student = Database::fetch_one(
    "SELECT s.*, u.email, u.id AS user_id FROM students s
     JOIN users u ON s.user_id = u.id
     WHERE s.id = ?",
    [$student_id]
);

if (!$student) {
    flash('error', 'Student not found.');
    redirect(BASE_URL . '/students/list.php');
}

// ============================================================================
// Handle POST request (actual soft delete execution)
// ============================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Verify CSRF token
    if (!csrf_verify()) {
        flash('error', 'Invalid form submission. Please try again.');
        redirect(BASE_URL . '/students/delete.php?id=' . $student_id);
    }

    try {
        // Soft delete — set is_active to 0 in the users table
        // This deactivates the student account without removing records
        Database::query(
            "UPDATE users SET is_active = 0 WHERE id = ?",
            [$student['user_id']]
        );

        // Flash success message
        $student_name = sanitize($student['first_name'] . ' ' . $student['last_name']);
        flash('success', 'Student "' . $student_name . '" has been deactivated successfully.');

        // Redirect to student list
        redirect(BASE_URL . '/students/list.php');

    } catch (Exception $e) {
        // Log error and show user-friendly message
        error_log('Error deactivating student ID ' . $student_id . ': ' . $e->getMessage());
        flash('error', 'An error occurred while deactivating the student. Please try again.');
        redirect(BASE_URL . '/students/delete.php?id=' . $student_id);
    }
}

// ============================================================================
// Display confirmation page (GET request)
// ============================================================================

?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <!-- Confirmation Card -->
            <div class="card border-danger shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>Confirm Deactivation
                    </h5>
                </div>

                <div class="card-body p-4">
                    <!-- Warning Message -->
                    <div class="alert alert-warning d-flex" role="alert">
                        <i class="bi bi-exclamation-circle me-3 flex-shrink-0"></i>
                        <div>
                            <strong>Are you sure?</strong> Deactivating this student will prevent them from logging in
                            to the system. However, all their records will be preserved in the database.
                        </div>
                    </div>

                    <!-- Student Details -->
                    <h6 class="mb-3 text-muted">Student Details:</h6>
                    <div class="list-group list-group-flush mb-4">
                        <div class="list-group-item px-0">
                            <div class="row">
                                <div class="col-sm-4 text-muted">Name:</div>
                                <div class="col-sm-8">
                                    <strong><?php echo sanitize($student['first_name'] . ' ' . $student['last_name']); ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item px-0">
                            <div class="row">
                                <div class="col-sm-4 text-muted">Student Number:</div>
                                <div class="col-sm-8">
                                    <strong><?php echo sanitize($student['student_number']); ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item px-0">
                            <div class="row">
                                <div class="col-sm-4 text-muted">Email:</div>
                                <div class="col-sm-8">
                                    <strong><?php echo sanitize($student['email']); ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item px-0">
                            <div class="row">
                                <div class="col-sm-4 text-muted">Current Status:</div>
                                <div class="col-sm-8">
                                    <span class="badge bg-success">Active</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Information Box -->
                    <div class="alert alert-info" role="alert">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>What happens after deactivation?</strong>
                        <ul class="mb-0 mt-2">
                            <li>The student cannot log in</li>
                            <li>Their account will appear as inactive in reports</li>
                            <li>All student records remain in the database</li>
                            <li>This action can be reversed by reactivating the account</li>
                        </ul>
                    </div>

                    <!-- Action Form -->
                    <form method="POST" action="<?php echo BASE_URL; ?>/students/delete.php?id=<?php echo $student_id; ?>">
                        <!-- CSRF Token -->
                        <?php csrf_field(); ?>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-danger flex-grow-1">
                                <i class="bi bi-trash me-2"></i>Yes, Deactivate
                            </button>
                            <a href="<?php echo BASE_URL; ?>/students/view.php?id=<?php echo $student_id; ?>" class="btn btn-secondary flex-grow-1">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Back to List Link -->
            <div class="mt-3 text-center">
                <a href="<?php echo BASE_URL; ?>/students/list.php" class="text-muted text-decoration-none">
                    <i class="bi bi-arrow-left me-1"></i>Back to Student List
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
