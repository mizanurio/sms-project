<?php
/**
 * add.php — Add Enrollment (Admin Only)
 *
 * Enroll a student in a course. The unique constraint
 * on (student_id, course_id) prevents duplicate enrollments.
 */

$page_title = 'Add Enrollment';
require_once __DIR__ . '/../../includes/header.php';
require_role('admin');

// Fetch active students for dropdown
$students = Database::fetch_all(
    "SELECT s.id, s.student_number, s.first_name, s.last_name
     FROM students s JOIN users u ON s.user_id = u.id
     WHERE u.is_active = 1 ORDER BY s.first_name"
);

// Fetch courses for dropdown
$courses = Database::fetch_all("SELECT id, course_code, course_name FROM courses ORDER BY course_code");

$form = ['student_id' => '', 'course_id' => '', 'enrollment_date' => date('Y-m-d')];
$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $errors[] = 'Invalid form submission.';
    } else {
        $form['student_id']      = intval($_POST['student_id'] ?? 0);
        $form['course_id']       = intval($_POST['course_id'] ?? 0);
        $form['enrollment_date'] = trim($_POST['enrollment_date'] ?? date('Y-m-d'));

        // Validate
        if ($form['student_id'] <= 0) $errors[] = 'Please select a student.';
        if ($form['course_id'] <= 0) $errors[] = 'Please select a course.';
        if (empty($form['enrollment_date'])) $errors[] = 'Enrollment date is required.';

        // Check for duplicate enrollment
        if (empty($errors)) {
            $existing = Database::fetch_one(
                "SELECT id, status FROM enrollments WHERE student_id = ? AND course_id = ?",
                [$form['student_id'], $form['course_id']]
            );
            if ($existing) {
                $errors[] = 'This student is already enrolled in this course (status: ' . $existing['status'] . ').';
            }
        }

        // Insert enrollment
        if (empty($errors)) {
            Database::query(
                "INSERT INTO enrollments (student_id, course_id, enrollment_date, status) VALUES (?, ?, ?, 'active')",
                [$form['student_id'], $form['course_id'], $form['enrollment_date']]
            );
            flash('success', 'Student enrolled successfully.');
            redirect(BASE_URL . '/enrollments/list.php');
        }
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-plus-circle me-2"></i>Add Enrollment</h1>
    <a href="<?php echo BASE_URL; ?>/enrollments/list.php" class="btn btn-secondary">
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

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="student_id" class="form-label">Student <span class="text-danger">*</span></label>
                    <select class="form-select" id="student_id" name="student_id" required>
                        <option value="">-- Select Student --</option>
                        <?php foreach ($students as $s): ?>
                            <option value="<?php echo $s['id']; ?>"
                                <?php echo ($form['student_id'] == $s['id']) ? 'selected' : ''; ?>>
                                <?php echo sanitize($s['student_number'] . ' — ' . $s['first_name'] . ' ' . $s['last_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="course_id" class="form-label">Course <span class="text-danger">*</span></label>
                    <select class="form-select" id="course_id" name="course_id" required>
                        <option value="">-- Select Course --</option>
                        <?php foreach ($courses as $c): ?>
                            <option value="<?php echo $c['id']; ?>"
                                <?php echo ($form['course_id'] == $c['id']) ? 'selected' : ''; ?>>
                                <?php echo sanitize($c['course_code'] . ' — ' . $c['course_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="enrollment_date" class="form-label">Enrollment Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="enrollment_date" name="enrollment_date"
                           value="<?php echo sanitize($form['enrollment_date']); ?>" required>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Enroll Student</button>
                <a href="<?php echo BASE_URL; ?>/enrollments/list.php" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
