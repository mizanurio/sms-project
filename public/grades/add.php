<?php
/**
 * add.php — Add Grade (Teacher and Admin)
 *
 * Add a grade for a specific enrollment.
 * Teachers can only grade their own course enrollments.
 * Grade letter is auto-calculated from percentage.
 */

$page_title = 'Add Grade';
require_once __DIR__ . '/../../includes/header.php';
require_role(['admin', 'teacher']);

$user = current_user();

// Get available enrollments based on role
if ($user['role'] === 'teacher') {
    $teacher = Database::fetch_one("SELECT id FROM teachers WHERE user_id = ?", [$user['id']]);
    if (!$teacher) {
        flash('error', 'Teacher profile not found.');
        redirect(BASE_URL . '/dashboard.php');
    }
    $enrollments = Database::fetch_all(
        "SELECT e.id, s.student_number, s.first_name, s.last_name, c.course_code, c.course_name
         FROM enrollments e
         JOIN students s ON e.student_id = s.id
         JOIN courses c ON e.course_id = c.id
         WHERE c.teacher_id = ? AND e.status = 'active'
         ORDER BY c.course_code, s.last_name",
        [$teacher['id']]
    );
} else {
    $enrollments = Database::fetch_all(
        "SELECT e.id, s.student_number, s.first_name, s.last_name, c.course_code, c.course_name
         FROM enrollments e
         JOIN students s ON e.student_id = s.id
         JOIN courses c ON e.course_id = c.id
         WHERE e.status = 'active'
         ORDER BY c.course_code, s.last_name"
    );
}

$form = ['enrollment_id' => $_GET['enrollment_id'] ?? '', 'assessment_name' => '', 'marks_obtained' => '', 'max_marks' => '100', 'comments' => ''];
$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $errors[] = 'Invalid form submission.';
    } else {
        $form['enrollment_id']   = intval($_POST['enrollment_id'] ?? 0);
        $form['assessment_name'] = trim($_POST['assessment_name'] ?? '');
        $form['marks_obtained']  = floatval($_POST['marks_obtained'] ?? 0);
        $form['max_marks']       = floatval($_POST['max_marks'] ?? 100);
        $form['comments']        = trim($_POST['comments'] ?? '');

        if ($form['enrollment_id'] <= 0) $errors[] = 'Please select an enrollment.';
        if (empty($form['assessment_name'])) $errors[] = 'Assessment name is required.';
        if ($form['marks_obtained'] < 0) $errors[] = 'Marks cannot be negative.';
        if ($form['max_marks'] <= 0) $errors[] = 'Maximum marks must be greater than 0.';
        if ($form['marks_obtained'] > $form['max_marks']) $errors[] = 'Marks obtained cannot exceed maximum marks.';

        // Verify enrollment belongs to teacher's course (if teacher role)
        if (empty($errors) && $user['role'] === 'teacher') {
            $valid = Database::fetch_one(
                "SELECT e.id FROM enrollments e JOIN courses c ON e.course_id = c.id WHERE e.id = ? AND c.teacher_id = ?",
                [$form['enrollment_id'], $teacher['id']]
            );
            if (!$valid) $errors[] = 'You can only add grades for your own course enrollments.';
        }

        if (empty($errors)) {
            // Auto-calculate grade letter
            $grade_letter = calculate_grade($form['marks_obtained'], $form['max_marks']);

            Database::query(
                "INSERT INTO grades (enrollment_id, assessment_name, marks_obtained, max_marks, grade_letter, comments)
                 VALUES (?, ?, ?, ?, ?, ?)",
                [$form['enrollment_id'], $form['assessment_name'], $form['marks_obtained'],
                 $form['max_marks'], $grade_letter, $form['comments']]
            );
            flash('success', 'Grade recorded successfully. Grade: ' . $grade_letter);
            redirect(BASE_URL . '/grades/list.php');
        }
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-plus-circle me-2"></i>Add Grade</h1>
    <a href="<?php echo BASE_URL; ?>/grades/list.php" class="btn btn-secondary">
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

            <div class="mb-3">
                <label for="enrollment_id" class="form-label">Student / Course <span class="text-danger">*</span></label>
                <select class="form-select" id="enrollment_id" name="enrollment_id" required>
                    <option value="">-- Select Enrollment --</option>
                    <?php foreach ($enrollments as $e): ?>
                        <option value="<?php echo $e['id']; ?>"
                            <?php echo ($form['enrollment_id'] == $e['id']) ? 'selected' : ''; ?>>
                            <?php echo sanitize($e['course_code'] . ' — ' . $e['first_name'] . ' ' . $e['last_name'] . ' (' . $e['student_number'] . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="assessment_name" class="form-label">Assessment Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="assessment_name" name="assessment_name"
                       value="<?php echo sanitize($form['assessment_name']); ?>" required
                       placeholder="e.g. Assignment 1, Mid-Term Exam, Quiz 1">
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="marks_obtained" class="form-label">Marks Obtained <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="marks_obtained" name="marks_obtained"
                           value="<?php echo sanitize($form['marks_obtained']); ?>" min="0" step="0.01" required>
                </div>
                <div class="col-md-4">
                    <label for="max_marks" class="form-label">Maximum Marks <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="max_marks" name="max_marks"
                           value="<?php echo sanitize($form['max_marks']); ?>" min="1" step="0.01" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Grade Scale</label>
                    <div class="form-text mt-0">
                        HD &ge; 85% | D &ge; 75% | C &ge; 65% | P &ge; 50% | F &lt; 50%
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="comments" class="form-label">Comments</label>
                <textarea class="form-control" id="comments" name="comments" rows="2"><?php echo sanitize($form['comments']); ?></textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Record Grade</button>
                <a href="<?php echo BASE_URL; ?>/grades/list.php" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
