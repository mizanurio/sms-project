<?php
/**
 * report.php — Attendance Report (Teacher and Admin)
 *
 * Shows attendance percentage per student for a selected course.
 * Teachers can only view reports for their own courses.
 */

$page_title = 'Attendance Report';
require_once __DIR__ . '/../../includes/header.php';
require_role(['admin', 'teacher']);

$user = current_user();

// Get available courses based on role
if ($user['role'] === 'teacher') {
    $teacher = Database::fetch_one("SELECT id FROM teachers WHERE user_id = ?", [$user['id']]);
    if (!$teacher) {
        flash('error', 'Teacher profile not found.');
        redirect(BASE_URL . '/dashboard.php');
    }
    $courses = Database::fetch_all(
        "SELECT id, course_code, course_name FROM courses WHERE teacher_id = ? ORDER BY course_code",
        [$teacher['id']]
    );
} else {
    $courses = Database::fetch_all("SELECT id, course_code, course_name FROM courses ORDER BY course_code");
}

$selected_course = intval($_GET['course_id'] ?? 0);
$report_data = [];

if ($selected_course > 0) {
    // Verify teacher access
    if ($user['role'] === 'teacher') {
        $valid = Database::fetch_one("SELECT id FROM courses WHERE id = ? AND teacher_id = ?", [$selected_course, $teacher['id']]);
        if (!$valid) {
            flash('error', 'You can only view reports for your own courses.');
            redirect(BASE_URL . '/attendance/report.php');
        }
    }

    // Fetch attendance stats per student
    $report_data = Database::fetch_all(
        "SELECT s.student_number, s.first_name, s.last_name,
                COUNT(a.id) as total_records,
                SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present_count,
                SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) as absent_count,
                SUM(CASE WHEN a.status = 'late' THEN 1 ELSE 0 END) as late_count,
                SUM(CASE WHEN a.status = 'excused' THEN 1 ELSE 0 END) as excused_count
         FROM enrollments e
         JOIN students s ON e.student_id = s.id
         LEFT JOIN attendance a ON a.enrollment_id = e.id
         WHERE e.course_id = ? AND e.status = 'active'
         GROUP BY s.id, s.student_number, s.first_name, s.last_name
         ORDER BY s.last_name, s.first_name",
        [$selected_course]
    );
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-bar-chart me-2"></i>Attendance Report</h1>
    <a href="<?php echo BASE_URL; ?>/attendance/mark.php" class="btn btn-primary">
        <i class="bi bi-calendar-check me-1"></i>Mark Attendance
    </a>
</div>

<!-- Course Selection -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-6">
                <label class="form-label">Select Course</label>
                <select name="course_id" class="form-select" required>
                    <option value="">-- Select Course --</option>
                    <?php foreach ($courses as $c): ?>
                        <option value="<?php echo $c['id']; ?>" <?php echo ($selected_course == $c['id']) ? 'selected' : ''; ?>>
                            <?php echo sanitize($c['course_code'] . ' — ' . $c['course_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-bar-chart me-1"></i>View Report</button>
            </div>
            <div class="col-md-3">
                <a href="<?php echo BASE_URL; ?>/attendance/report.php" class="btn btn-outline-secondary w-100">Clear</a>
            </div>
        </form>
    </div>
</div>

<?php if ($selected_course > 0): ?>
    <?php if (empty($report_data)): ?>
        <div class="alert alert-info"><i class="bi bi-info-circle me-1"></i>No enrollment data found for this course.</div>
    <?php else: ?>
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Attendance Summary — <?php echo count($report_data); ?> student(s)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Student</th>
                                <th>Present</th>
                                <th>Absent</th>
                                <th>Late</th>
                                <th>Excused</th>
                                <th>Total</th>
                                <th>Attendance %</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($report_data as $r): ?>
                                <?php
                                // Calculate attendance percentage (present + late count as attended)
                                $attended = $r['present_count'] + $r['late_count'];
                                $pct = ($r['total_records'] > 0) ? round(($attended / $r['total_records']) * 100, 1) : 0;
                                $pct_class = ($pct >= 80) ? 'success' : (($pct >= 60) ? 'warning' : 'danger');
                                ?>
                                <tr>
                                    <td>
                                        <?php echo sanitize($r['first_name'] . ' ' . $r['last_name']); ?>
                                        <br><small class="text-muted"><?php echo sanitize($r['student_number']); ?></small>
                                    </td>
                                    <td><span class="badge bg-success"><?php echo $r['present_count']; ?></span></td>
                                    <td><span class="badge bg-danger"><?php echo $r['absent_count']; ?></span></td>
                                    <td><span class="badge bg-warning text-dark"><?php echo $r['late_count']; ?></span></td>
                                    <td><span class="badge bg-info"><?php echo $r['excused_count']; ?></span></td>
                                    <td><?php echo $r['total_records']; ?></td>
                                    <td>
                                        <div class="progress" style="min-width: 100px;">
                                            <div class="progress-bar bg-<?php echo $pct_class; ?>"
                                                 style="width: <?php echo $pct; ?>%"
                                                 role="progressbar">
                                                <?php echo $pct; ?>%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
