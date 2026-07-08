<?php
/**
 * view.php — View Course Details (Admin Only)
 *
 * Displays full course information and enrolled students list.
 */

$page_title = 'View Course';
require_once __DIR__ . '/../../includes/header.php';
require_role('admin');

// Get course ID
$course_id = intval($_GET['id'] ?? 0);

// Fetch course with teacher info
$course = Database::fetch_one(
    "SELECT c.*, t.first_name as teacher_first, t.last_name as teacher_last, t.employee_number
     FROM courses c LEFT JOIN teachers t ON c.teacher_id = t.id
     WHERE c.id = ?",
    [$course_id]
);

if (!$course) {
    flash('error', 'Course not found.');
    redirect(BASE_URL . '/courses/list.php');
}

// Fetch enrolled students
$enrollments = Database::fetch_all(
    "SELECT s.student_number, s.first_name, s.last_name, e.enrollment_date, e.status, e.id as enrollment_id
     FROM enrollments e JOIN students s ON e.student_id = s.id
     WHERE e.course_id = ? ORDER BY s.last_name, s.first_name",
    [$course_id]
);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-book me-2"></i>Course Details</h1>
    <div class="d-flex gap-2">
        <a href="<?php echo BASE_URL; ?>/courses/edit.php?id=<?php echo $course_id; ?>" class="btn btn-warning">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
        <a href="<?php echo BASE_URL; ?>/courses/list.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>
</div>

<!-- Course Information -->
<div class="row g-4 mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white"><h5 class="mb-0">Course Information</h5></div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-4 fw-bold">Course Code:</div>
                    <div class="col-sm-8"><span class="badge bg-info fs-6"><?php echo sanitize($course['course_code']); ?></span></div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-4 fw-bold">Course Name:</div>
                    <div class="col-sm-8"><?php echo sanitize($course['course_name']); ?></div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-4 fw-bold">Description:</div>
                    <div class="col-sm-8"><?php echo !empty($course['description']) ? sanitize($course['description']) : '<span class="text-muted">No description</span>'; ?></div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-4 fw-bold">Credits:</div>
                    <div class="col-sm-8"><?php echo intval($course['credits']); ?></div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-4 fw-bold">Semester / Year:</div>
                    <div class="col-sm-8">
                        <?php
                        $sem = ($course['semester'] === 'summer') ? 'Summer' : 'Semester ' . $course['semester'];
                        echo sanitize($sem . ', ' . $course['year']);
                        ?>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-4 fw-bold">Teacher:</div>
                    <div class="col-sm-8">
                        <?php if ($course['teacher_first']): ?>
                            <?php echo sanitize($course['teacher_first'] . ' ' . $course['teacher_last']); ?>
                            <small class="text-muted">(<?php echo sanitize($course['employee_number']); ?>)</small>
                        <?php else: ?>
                            <span class="text-muted">Unassigned</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row mb-0">
                    <div class="col-sm-4 fw-bold">Created:</div>
                    <div class="col-sm-8"><?php echo format_datetime($course['created_at']); ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-primary">
            <div class="card-body text-center">
                <i class="bi bi-people text-primary" style="font-size: 3rem;"></i>
                <h2 class="mt-2"><?php echo count($enrollments); ?></h2>
                <p class="text-muted mb-0">Enrolled Students</p>
            </div>
        </div>
    </div>
</div>

<!-- Enrolled Students -->
<div class="card">
    <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-people me-2"></i>Enrolled Students</h5></div>
    <div class="card-body">
        <?php if (empty($enrollments)): ?>
            <p class="text-muted">No students enrolled in this course.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr><th>Student Number</th><th>Name</th><th>Enrollment Date</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($enrollments as $e): ?>
                            <tr>
                                <td><?php echo sanitize($e['student_number']); ?></td>
                                <td><?php echo sanitize($e['first_name'] . ' ' . $e['last_name']); ?></td>
                                <td><?php echo format_date($e['enrollment_date']); ?></td>
                                <td>
                                    <?php
                                    $badge = ($e['status'] === 'active') ? 'success' : (($e['status'] === 'dropped') ? 'danger' : 'secondary');
                                    ?>
                                    <span class="badge bg-<?php echo $badge; ?>"><?php echo ucfirst(sanitize($e['status'])); ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
