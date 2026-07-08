<?php
/**
 * edit.php — Edit Grade (Teacher and Admin)
 *
 * Allows editing an existing grade record.
 * Teachers can only edit grades for their own courses.
 * Grade letter is recalculated on save.
 */

$page_title = 'Edit Grade';
require_once __DIR__ . '/../../includes/header.php';
require_role(['admin', 'teacher']);

$user = current_user();
$grade_id = intval($_GET['id'] ?? 0);

// Fetch grade with enrollment details
$grade = Database::fetch_one(
    "SELECT g.*, e.student_id, e.course_id, c.teacher_id, c.course_code, c.course_name,
            s.first_name as s_first, s.last_name as s_last, s.student_number
     FROM grades g
     JOIN enrollments e ON g.enrollment_id = e.id
     JOIN courses c ON e.course_id = c.id
     JOIN students s ON e.student_id = s.id
     WHERE g.id = ?",
    [$grade_id]
);

if (!$grade) {
    flash('error', 'Grade record not found.');
    redirect(BASE_URL . '/grades/list.php');
}

// If teacher, verify they own this course
if ($user['role'] === 'teacher') {
    $teacher = Database::fetch_one("SELECT id FROM teachers WHERE user_id = ?", [$user['id']]);
    if (!$teacher || $grade['teacher_id'] != $teacher['id']) {
        flash('error', 'You do not have permission to edit this grade.');
        redirect(BASE_URL . '/grades/list.php');
    }
}

// Pre-fill form
$form = [
    'assessment_name' => $grade['assessment_name'],
    'marks_obtained'  => $grade['marks_obtained'],
    'max_marks'       => $grade['max_marks'],
    'comments'        => $grade['comments'],
];
$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $errors[] = 'Invalid form submission.';
    } else {
        $form['assessment_name'] = trim($_POST['assessment_name'] ?? '');
        $form['marks_obtained']  = floatval($_POST['marks_obtained'] ?? 0);
        $form['max_marks']       = floatval($_POST['max_marks'] ?? 100);
        $form['comments']        = trim($_POST['comments'] ?? '');

        if (empty($form['assessment_name'])) $errors[] = 'Assessment name is required.';
        if ($form['marks_obtained'] < 0) $errors[] = 'Marks cannot be negative.';
        if ($form['max_marks'] <= 0) $errors[] = 'Maximum marks must be greater than 0.';
        if ($form['marks_obtained'] > $form['max_marks']) $errors[] = 'Marks cannot exceed maximum.';

        if (empty($errors)) {
            $grade_letter = calculate_grade($form['marks_obtained'], $form['max_marks']);
            Database::query(
                "UPDATE grades SET assessment_name = ?, marks_obtained = ?, max_marks = ?,
                 grade_letter = ?, comments = ? WHERE id = ?",
                [$form['assessment_name'], $form['marks_obtained'], $form['max_marks'],
                 $grade_letter, $form['comments'], $grade_id]
            );
            flash('success', 'Grade updated successfully. New grade: ' . $grade_letter);
            redirect(BASE_URL . '/grades/list.php');
        }
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-pencil me-2"></i>Edit Grade</h1>
    <a href="<?php echo BASE_URL; ?>/grades/list.php" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to List
    </a>
</div>

<!-- Student and Course info (read-only) -->
<div class="alert alert-light border mb-4">
    <strong>Student:</strong> <?php echo sanitize($grade['s_first'] . ' ' . $grade['s_last']); ?>
    (<?php echo sanitize($grade['student_number']); ?>) &nbsp;|&nbsp;
    <strong>Course:</strong> <?php echo sanitize($grade['course_code'] . ' — ' . $grade['course_name']); ?>
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
                <label for="assessment_name" class="form-label">Assessment Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="assessment_name" name="assessment_name"
                       value="<?php echo sanitize($form['assessment_name']); ?>" required>
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
                    <label class="form-label">Current Grade</label>
                    <div class="mt-1">
                        <span class="badge bg-secondary fs-6"><?php echo sanitize($grade['grade_letter']); ?></span>
                        <small class="text-muted ms-1">(will be recalculated on save)</small>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="comments" class="form-label">Comments</label>
                <textarea class="form-control" id="comments" name="comments" rows="2"><?php echo sanitize($form['comments']); ?></textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Save Changes</button>
                <a href="<?php echo BASE_URL; ?>/grades/list.php" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
