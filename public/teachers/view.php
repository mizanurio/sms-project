<?php
/**
 * view.php — View Teacher Details (Admin Only)
 *
 * Displays teacher profile and assigned courses.
 */

$page_title = 'View Teacher';
require_once __DIR__ . '/../../includes/header.php';
require_role('admin');

$teacher_id = intval($_GET['id'] ?? 0);

$teacher = Database::fetch_one(
    "SELECT t.*, u.email, u.username, u.created_at as account_created
     FROM teachers t JOIN users u ON t.user_id = u.id WHERE t.id = ?",
    [$teacher_id]
);

if (!$teacher) {
    flash('error', 'Teacher not found.');
    redirect(BASE_URL . '/teachers/list.php');
}

// Fetch assigned courses
$courses = Database::fetch_all(
    "SELECT c.course_code, c.course_name, c.credits, c.semester, c.year,
            (SELECT COUNT(*) FROM enrollments e WHERE e.course_id = c.id AND e.status = 'active') as student_count
     FROM courses c WHERE c.teacher_id = ? ORDER BY c.year DESC, c.semester DESC",
    [$teacher_id]
);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-person-badge me-2"></i>Teacher Details</h1>
    <div class="d-flex gap-2">
        <a href="<?php echo BASE_URL; ?>/teachers/edit.php?id=<?php echo $teacher_id; ?>" class="btn btn-warning">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
        <a href="<?php echo BASE_URL; ?>/teachers/list.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white"><h5 class="mb-0">Personal Information</h5></div>
            <div class="card-body">
                <div class="row mb-2"><div class="col-sm-4 fw-bold">Employee Number:</div><div class="col-sm-8"><span class="badge bg-success fs-6"><?php echo sanitize($teacher['employee_number']); ?></span></div></div>
                <div class="row mb-2"><div class="col-sm-4 fw-bold">Full Name:</div><div class="col-sm-8"><?php echo sanitize($teacher['first_name'] . ' ' . $teacher['last_name']); ?></div></div>
                <div class="row mb-2"><div class="col-sm-4 fw-bold">Department:</div><div class="col-sm-8"><?php echo sanitize($teacher['department']); ?></div></div>
                <div class="row mb-2"><div class="col-sm-4 fw-bold">Phone:</div><div class="col-sm-8"><?php echo sanitize($teacher['phone'] ?? 'N/A'); ?></div></div>
                <div class="row mb-2"><div class="col-sm-4 fw-bold">Hire Date:</div><div class="col-sm-8"><?php echo format_date($teacher['hire_date']); ?></div></div>
                <div class="row mb-2"><div class="col-sm-4 fw-bold">Email:</div><div class="col-sm-8"><?php echo sanitize($teacher['email']); ?></div></div>
                <div class="row mb-0"><div class="col-sm-4 fw-bold">Account Created:</div><div class="col-sm-8"><?php echo format_datetime($teacher['account_created']); ?></div></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-success">
            <div class="card-body text-center">
                <i class="bi bi-book text-success" style="font-size: 3rem;"></i>
                <h2 class="mt-2"><?php echo count($courses); ?></h2>
                <p class="text-muted mb-0">Assigned Courses</p>
            </div>
        </div>
    </div>
</div>

<!-- Assigned Courses -->
<div class="card">
    <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-book me-2"></i>Assigned Courses</h5></div>
    <div class="card-body">
        <?php if (empty($courses)): ?>
            <p class="text-muted">No courses assigned.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead><tr><th>Code</th><th>Course Name</th><th>Credits</th><th>Semester</th><th>Students</th></tr></thead>
                    <tbody>
                        <?php foreach ($courses as $c): ?>
                            <tr>
                                <td><span class="badge bg-info"><?php echo sanitize($c['course_code']); ?></span></td>
                                <td><?php echo sanitize($c['course_name']); ?></td>
                                <td><?php echo $c['credits']; ?></td>
                                <td><?php echo ($c['semester'] === 'summer' ? 'Summer' : 'Sem ' . $c['semester']) . ', ' . $c['year']; ?></td>
                                <td><span class="badge bg-primary"><?php echo $c['student_count']; ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
