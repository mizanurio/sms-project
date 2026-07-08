<?php
/**
 * edit.php — Edit Course (Admin Only)
 *
 * Form to update an existing course's details and teacher assignment.
 */

$page_title = 'Edit Course';
require_once __DIR__ . '/../../includes/header.php';
require_role('admin');

$course_id = intval($_GET['id'] ?? 0);

// Fetch current course data
$course = Database::fetch_one("SELECT * FROM courses WHERE id = ?", [$course_id]);
if (!$course) {
    flash('error', 'Course not found.');
    redirect(BASE_URL . '/courses/list.php');
}

// Fetch active teachers for dropdown
$teachers = Database::fetch_all(
    "SELECT t.id, t.first_name, t.last_name, t.department
     FROM teachers t JOIN users u ON t.user_id = u.id
     WHERE u.is_active = 1 ORDER BY t.first_name"
);

// Pre-fill form with current data
$form = [
    'course_code' => $course['course_code'],
    'course_name' => $course['course_name'],
    'description' => $course['description'],
    'credits'     => $course['credits'],
    'teacher_id'  => $course['teacher_id'],
    'semester'    => $course['semester'],
    'year'        => $course['year'],
];
$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $errors[] = 'Invalid form submission.';
    } else {
        $form['course_code']  = strtoupper(trim($_POST['course_code'] ?? ''));
        $form['course_name']  = trim($_POST['course_name'] ?? '');
        $form['description']  = trim($_POST['description'] ?? '');
        $form['credits']      = intval($_POST['credits'] ?? 3);
        $form['teacher_id']   = $_POST['teacher_id'] ?? '';
        $form['semester']     = $_POST['semester'] ?? '1';
        $form['year']         = intval($_POST['year'] ?? date('Y'));

        if (empty($form['course_code'])) $errors[] = 'Course code is required.';
        if (empty($form['course_name'])) $errors[] = 'Course name is required.';

        // Check course_code uniqueness (exclude this course)
        if (empty($errors)) {
            $existing = Database::fetch_one(
                "SELECT id FROM courses WHERE course_code = ? AND id != ?",
                [$form['course_code'], $course_id]
            );
            if ($existing) $errors[] = 'This course code is already used by another course.';
        }

        if (empty($errors)) {
            $teacher_id = !empty($form['teacher_id']) ? intval($form['teacher_id']) : null;
            Database::query(
                "UPDATE courses SET course_code = ?, course_name = ?, description = ?,
                 credits = ?, teacher_id = ?, semester = ?, year = ? WHERE id = ?",
                [$form['course_code'], $form['course_name'], $form['description'],
                 $form['credits'], $teacher_id, $form['semester'], $form['year'], $course_id]
            );
            flash('success', 'Course updated successfully.');
            redirect(BASE_URL . '/courses/view.php?id=' . $course_id);
        }
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-pencil me-2"></i>Edit Course</h1>
    <a href="<?php echo BASE_URL; ?>/courses/list.php" class="btn btn-secondary">
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
                <div class="col-md-4">
                    <label for="course_code" class="form-label">Course Code <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="course_code" name="course_code"
                           value="<?php echo sanitize($form['course_code']); ?>" required>
                </div>
                <div class="col-md-8">
                    <label for="course_name" class="form-label">Course Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="course_name" name="course_name"
                           value="<?php echo sanitize($form['course_name']); ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?php echo sanitize($form['description']); ?></textarea>
            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="credits" class="form-label">Credits <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="credits" name="credits"
                           value="<?php echo intval($form['credits']); ?>" min="1" max="12" required>
                </div>
                <div class="col-md-3">
                    <label for="semester" class="form-label">Semester</label>
                    <select class="form-select" id="semester" name="semester">
                        <option value="1" <?php echo ($form['semester'] === '1') ? 'selected' : ''; ?>>Semester 1</option>
                        <option value="2" <?php echo ($form['semester'] === '2') ? 'selected' : ''; ?>>Semester 2</option>
                        <option value="summer" <?php echo ($form['semester'] === 'summer') ? 'selected' : ''; ?>>Summer</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="year" class="form-label">Year</label>
                    <input type="number" class="form-control" id="year" name="year"
                           value="<?php echo intval($form['year']); ?>" min="2020" max="2030">
                </div>
                <div class="col-md-3">
                    <label for="teacher_id" class="form-label">Assign Teacher</label>
                    <select class="form-select" id="teacher_id" name="teacher_id">
                        <option value="">-- No Teacher --</option>
                        <?php foreach ($teachers as $t): ?>
                            <option value="<?php echo $t['id']; ?>"
                                <?php echo ($form['teacher_id'] == $t['id']) ? 'selected' : ''; ?>>
                                <?php echo sanitize($t['first_name'] . ' ' . $t['last_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Save Changes</button>
                <a href="<?php echo BASE_URL; ?>/courses/view.php?id=<?php echo $course_id; ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
