<?php
/**
 * dashboard.php — Role-Based Dashboard
 *
 * Displays different dashboard content based on the user's role:
 * - Admin: system-wide statistics and recent activity
 * - Teacher: assigned courses and recent grade entries
 * - Student: enrolled courses, grades, and attendance summary
 */

$page_title = 'Dashboard';
require_once __DIR__ . '/../includes/header.php';

// Require the user to be logged in
require_login();

// Get the current user's information
$user = current_user();
$display_name = get_display_name($user);
?>

<!-- Welcome banner -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="bi bi-speedometer2 me-2"></i>Welcome, <?php echo sanitize($display_name); ?>!
    </h1>
    <span class="badge bg-primary fs-6"><?php echo ucfirst(sanitize($user['role'])); ?></span>
</div>

<?php
// ============================================================
// ADMIN DASHBOARD
// ============================================================
if ($user['role'] === 'admin'):

    // Fetch statistics from the database
    $total_students = Database::fetch_one("SELECT COUNT(*) as count FROM students s JOIN users u ON s.user_id = u.id WHERE u.is_active = 1")['count'];
    $total_teachers = Database::fetch_one("SELECT COUNT(*) as count FROM teachers t JOIN users u ON t.user_id = u.id WHERE u.is_active = 1")['count'];
    $total_courses = Database::fetch_one("SELECT COUNT(*) as count FROM courses")['count'];
    $total_enrollments = Database::fetch_one("SELECT COUNT(*) as count FROM enrollments WHERE status = 'active'")['count'];

    // Fetch recent enrollments (last 5)
    $recent_enrollments = Database::fetch_all(
        "SELECT s.first_name, s.last_name, c.course_name, e.enrollment_date
         FROM enrollments e
         JOIN students s ON e.student_id = s.id
         JOIN courses c ON e.course_id = c.id
         ORDER BY e.enrollment_date DESC, e.id DESC
         LIMIT 5"
    );
?>

<!-- Admin Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card card-dashboard border-start border-primary border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle text-muted mb-1">Total Students</h6>
                        <h2 class="card-title mb-0"><?php echo $total_students; ?></h2>
                    </div>
                    <i class="bi bi-people text-primary" style="font-size: 2.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-dashboard border-start border-success border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle text-muted mb-1">Total Teachers</h6>
                        <h2 class="card-title mb-0"><?php echo $total_teachers; ?></h2>
                    </div>
                    <i class="bi bi-person-badge text-success" style="font-size: 2.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-dashboard border-start border-info border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle text-muted mb-1">Total Courses</h6>
                        <h2 class="card-title mb-0"><?php echo $total_courses; ?></h2>
                    </div>
                    <i class="bi bi-book text-info" style="font-size: 2.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-dashboard border-start border-warning border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle text-muted mb-1">Active Enrollments</h6>
                        <h2 class="card-title mb-0"><?php echo $total_enrollments; ?></h2>
                    </div>
                    <i class="bi bi-card-checklist text-warning" style="font-size: 2.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Enrollments and Quick Links -->
<div class="row g-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Enrollments</h5>
            </div>
            <div class="card-body">
                <?php if (empty($recent_enrollments)): ?>
                    <p class="text-muted">No enrollments found.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Course</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_enrollments as $enrollment): ?>
                                    <tr>
                                        <td><?php echo sanitize($enrollment['first_name'] . ' ' . $enrollment['last_name']); ?></td>
                                        <td><?php echo sanitize($enrollment['course_name']); ?></td>
                                        <td><?php echo format_date($enrollment['enrollment_date']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Quick Links</h5>
            </div>
            <div class="card-body d-grid gap-2">
                <a href="<?php echo BASE_URL; ?>/students/add.php" class="btn btn-outline-primary">
                    <i class="bi bi-person-plus me-1"></i>Add Student
                </a>
                <a href="<?php echo BASE_URL; ?>/teachers/add.php" class="btn btn-outline-success">
                    <i class="bi bi-person-badge me-1"></i>Add Teacher
                </a>
                <a href="<?php echo BASE_URL; ?>/courses/add.php" class="btn btn-outline-info">
                    <i class="bi bi-book me-1"></i>Add Course
                </a>
                <a href="<?php echo BASE_URL; ?>/enrollments/add.php" class="btn btn-outline-warning">
                    <i class="bi bi-card-checklist me-1"></i>Add Enrollment
                </a>
            </div>
        </div>
    </div>
</div>

<?php
// ============================================================
// TEACHER DASHBOARD
// ============================================================
elseif ($user['role'] === 'teacher'):

    // Get teacher record
    $teacher = Database::fetch_one("SELECT * FROM teachers WHERE user_id = ?", [$user['id']]);

    if ($teacher):
        // Count my courses
        $my_courses_count = Database::fetch_one(
            "SELECT COUNT(*) as count FROM courses WHERE teacher_id = ?",
            [$teacher['id']]
        )['count'];

        // Count total students across my courses
        $my_students_count = Database::fetch_one(
            "SELECT COUNT(DISTINCT e.student_id) as count
             FROM enrollments e
             JOIN courses c ON e.course_id = c.id
             WHERE c.teacher_id = ? AND e.status = 'active'",
            [$teacher['id']]
        )['count'];

        // Get my courses with student counts
        $my_courses = Database::fetch_all(
            "SELECT c.id, c.course_code, c.course_name, c.semester, c.year,
                    COUNT(CASE WHEN e.status = 'active' THEN 1 END) as student_count
             FROM courses c
             LEFT JOIN enrollments e ON c.id = e.course_id
             WHERE c.teacher_id = ?
             GROUP BY c.id
             ORDER BY c.year DESC, c.semester DESC",
            [$teacher['id']]
        );

        // Get recent grade entries
        $recent_grades = Database::fetch_all(
            "SELECT g.assessment_name, g.marks_obtained, g.max_marks, g.grade_letter, g.recorded_at,
                    s.first_name, s.last_name, c.course_code
             FROM grades g
             JOIN enrollments e ON g.enrollment_id = e.id
             JOIN students s ON e.student_id = s.id
             JOIN courses c ON e.course_id = c.id
             WHERE c.teacher_id = ?
             ORDER BY g.recorded_at DESC
             LIMIT 5",
            [$teacher['id']]
        );
    endif;
?>

<?php if ($teacher): ?>
<!-- Teacher Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card card-dashboard border-start border-primary border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle text-muted mb-1">My Courses</h6>
                        <h2 class="card-title mb-0"><?php echo $my_courses_count; ?></h2>
                    </div>
                    <i class="bi bi-book text-primary" style="font-size: 2.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-dashboard border-start border-success border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle text-muted mb-1">Total Students</h6>
                        <h2 class="card-title mb-0"><?php echo $my_students_count; ?></h2>
                    </div>
                    <i class="bi bi-people text-success" style="font-size: 2.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-dashboard border-start border-info border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle text-muted mb-1">Department</h6>
                        <h5 class="card-title mb-0"><?php echo sanitize($teacher['department']); ?></h5>
                    </div>
                    <i class="bi bi-building text-info" style="font-size: 2.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- My Courses and Recent Grades -->
<div class="row g-4">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-book me-2"></i>My Courses</h5>
            </div>
            <div class="card-body">
                <?php if (empty($my_courses)): ?>
                    <p class="text-muted">No courses assigned yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead>
                                <tr><th>Code</th><th>Course Name</th><th>Semester</th><th>Students</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($my_courses as $course): ?>
                                    <tr>
                                        <td><?php echo sanitize($course['course_code']); ?></td>
                                        <td><?php echo sanitize($course['course_name']); ?></td>
                                        <td>Sem <?php echo sanitize($course['semester']); ?>, <?php echo sanitize($course['year']); ?></td>
                                        <td><span class="badge bg-primary"><?php echo $course['student_count']; ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-award me-2"></i>Recent Grade Entries</h5>
            </div>
            <div class="card-body">
                <?php if (empty($recent_grades)): ?>
                    <p class="text-muted">No grades recorded yet.</p>
                <?php else: ?>
                    <?php foreach ($recent_grades as $grade): ?>
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                            <div>
                                <strong><?php echo sanitize($grade['first_name'] . ' ' . $grade['last_name']); ?></strong>
                                <br><small class="text-muted"><?php echo sanitize($grade['course_code']); ?> — <?php echo sanitize($grade['assessment_name']); ?></small>
                            </div>
                            <span class="badge bg-<?php echo ($grade['grade_letter'] === 'HD' || $grade['grade_letter'] === 'D') ? 'success' : (($grade['grade_letter'] === 'F') ? 'danger' : 'warning'); ?>">
                                <?php echo sanitize($grade['grade_letter']); ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
    <div class="alert alert-warning">Teacher profile not found. Please contact an administrator.</div>
<?php endif; ?>

<?php
// ============================================================
// STUDENT DASHBOARD
// ============================================================
elseif ($user['role'] === 'student'):

    // Get student record
    $student = Database::fetch_one("SELECT * FROM students WHERE user_id = ?", [$user['id']]);

    if ($student):
        // Count active enrollments
        $enrolled_count = Database::fetch_one(
            "SELECT COUNT(*) as count FROM enrollments WHERE student_id = ? AND status = 'active'",
            [$student['id']]
        )['count'];

        // Calculate attendance percentage
        $attendance_stats = Database::fetch_one(
            "SELECT COUNT(*) as total,
                    SUM(CASE WHEN a.status IN ('present', 'late') THEN 1 ELSE 0 END) as attended
             FROM attendance a
             JOIN enrollments e ON a.enrollment_id = e.id
             WHERE e.student_id = ?",
            [$student['id']]
        );
        $attendance_pct = ($attendance_stats['total'] > 0)
            ? round(($attendance_stats['attended'] / $attendance_stats['total']) * 100, 1)
            : 0;

        // Get recent grades
        $recent_grades = Database::fetch_all(
            "SELECT g.assessment_name, g.marks_obtained, g.max_marks, g.grade_letter, g.recorded_at,
                    c.course_name, c.course_code
             FROM grades g
             JOIN enrollments e ON g.enrollment_id = e.id
             JOIN courses c ON e.course_id = c.id
             WHERE e.student_id = ?
             ORDER BY g.recorded_at DESC
             LIMIT 5",
            [$student['id']]
        );

        // Profile completeness
        $profile_fields = ['phone', 'address', 'date_of_birth', 'gender'];
        $filled = 0;
        foreach ($profile_fields as $field) {
            if (!empty($student[$field])) $filled++;
        }
        $profile_pct = round(($filled / count($profile_fields)) * 100);
    endif;
?>

<?php if ($student): ?>
<!-- Student Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card card-dashboard border-start border-primary border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle text-muted mb-1">Enrolled Courses</h6>
                        <h2 class="card-title mb-0"><?php echo $enrolled_count; ?></h2>
                    </div>
                    <i class="bi bi-book text-primary" style="font-size: 2.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-dashboard border-start border-success border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle text-muted mb-1">Attendance</h6>
                        <h2 class="card-title mb-0"><?php echo $attendance_pct; ?>%</h2>
                    </div>
                    <i class="bi bi-calendar-check text-success" style="font-size: 2.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-dashboard border-start border-info border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle text-muted mb-1">Student Number</h6>
                        <h5 class="card-title mb-0"><?php echo sanitize($student['student_number']); ?></h5>
                    </div>
                    <i class="bi bi-person-vcard text-info" style="font-size: 2.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-dashboard border-start border-warning border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle text-muted mb-1">Profile Complete</h6>
                        <h2 class="card-title mb-0"><?php echo $profile_pct; ?>%</h2>
                    </div>
                    <i class="bi bi-person-check text-warning" style="font-size: 2.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Grades -->
<div class="row g-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-award me-2"></i>My Recent Grades</h5>
            </div>
            <div class="card-body">
                <?php if (empty($recent_grades)): ?>
                    <p class="text-muted">No grades recorded yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead>
                                <tr><th>Course</th><th>Assessment</th><th>Marks</th><th>Grade</th><th>Date</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_grades as $grade): ?>
                                    <tr>
                                        <td><?php echo sanitize($grade['course_code'] . ' — ' . $grade['course_name']); ?></td>
                                        <td><?php echo sanitize($grade['assessment_name']); ?></td>
                                        <td><?php echo $grade['marks_obtained']; ?> / <?php echo $grade['max_marks']; ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo ($grade['grade_letter'] === 'HD' || $grade['grade_letter'] === 'D') ? 'success' : (($grade['grade_letter'] === 'F') ? 'danger' : 'warning'); ?>">
                                                <?php echo sanitize($grade['grade_letter']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo format_date($grade['recorded_at']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
    <div class="alert alert-warning">Student profile not found. Please contact an administrator.</div>
<?php endif; ?>

<?php endif; // End of role check ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
