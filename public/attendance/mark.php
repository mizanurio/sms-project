<?php
/**
 * mark.php — Mark Attendance (Teacher and Admin)
 *
 * Bulk attendance marking form for a course on a specific date.
 * Teachers can only mark attendance for their own courses.
 * Uses the unique constraint on (enrollment_id, attendance_date) to prevent duplicates.
 */

$page_title = 'Mark Attendance';
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

$selected_course = intval($_GET['course_id'] ?? ($_POST['course_id'] ?? 0));
$selected_date = $_GET['date'] ?? ($_POST['attendance_date'] ?? date('Y-m-d'));
$students = [];
$errors = [];
$show_form = false;

// If course and date selected, fetch enrolled students
if ($selected_course > 0 && !empty($selected_date)) {
    // Verify teacher owns this course
    if ($user['role'] === 'teacher') {
        $valid = Database::fetch_one("SELECT id FROM courses WHERE id = ? AND teacher_id = ?", [$selected_course, $teacher['id']]);
        if (!$valid) {
            flash('error', 'You can only mark attendance for your own courses.');
            redirect(BASE_URL . '/attendance/mark.php');
        }
    }

    // Fetch active enrollments with any existing attendance for this date
    $students = Database::fetch_all(
        "SELECT e.id as enrollment_id, s.student_number, s.first_name, s.last_name,
                a.status as existing_status, a.notes as existing_notes
         FROM enrollments e
         JOIN students s ON e.student_id = s.id
         LEFT JOIN attendance a ON a.enrollment_id = e.id AND a.attendance_date = ?
         WHERE e.course_id = ? AND e.status = 'active'
         ORDER BY s.last_name, s.first_name",
        [$selected_date, $selected_course]
    );
    $show_form = true;
}

// Handle bulk attendance submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_attendance'])) {
    if (!csrf_verify()) {
        $errors[] = 'Invalid form submission.';
    } else {
        $course_id = intval($_POST['course_id'] ?? 0);
        $att_date = trim($_POST['attendance_date'] ?? '');
        $statuses = $_POST['status'] ?? [];
        $notes = $_POST['notes'] ?? [];

        if ($course_id <= 0 || empty($att_date)) {
            $errors[] = 'Course and date are required.';
        }

        if (empty($errors)) {
            $saved = 0;
            foreach ($statuses as $enrollment_id => $status) {
                $enrollment_id = intval($enrollment_id);
                $status = in_array($status, ['present', 'absent', 'late', 'excused']) ? $status : 'present';
                $note = trim($notes[$enrollment_id] ?? '');

                // Check if attendance already exists for this enrollment and date
                $existing = Database::fetch_one(
                    "SELECT id FROM attendance WHERE enrollment_id = ? AND attendance_date = ?",
                    [$enrollment_id, $att_date]
                );

                if ($existing) {
                    // Update existing record
                    Database::query(
                        "UPDATE attendance SET status = ?, notes = ? WHERE id = ?",
                        [$status, $note ?: null, $existing['id']]
                    );
                } else {
                    // Insert new record
                    Database::query(
                        "INSERT INTO attendance (enrollment_id, attendance_date, status, notes) VALUES (?, ?, ?, ?)",
                        [$enrollment_id, $att_date, $status, $note ?: null]
                    );
                }
                $saved++;
            }
            flash('success', "Attendance marked for $saved student(s).");
            redirect(BASE_URL . '/attendance/mark.php?course_id=' . $course_id . '&date=' . $att_date);
        }
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-calendar-check me-2"></i>Mark Attendance</h1>
    <a href="<?php echo BASE_URL; ?>/attendance/report.php" class="btn btn-info">
        <i class="bi bi-bar-chart me-1"></i>View Reports
    </a>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0 ps-3">
            <?php foreach ($errors as $e): ?><li><?php echo sanitize($e); ?></li><?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Course and Date Selection -->
<div class="card mb-4">
    <div class="card-header bg-white"><h5 class="mb-0">Select Course and Date</h5></div>
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label for="course_id" class="form-label">Course</label>
                <select class="form-select" id="course_id" name="course_id" required>
                    <option value="">-- Select Course --</option>
                    <?php foreach ($courses as $c): ?>
                        <option value="<?php echo $c['id']; ?>" <?php echo ($selected_course == $c['id']) ? 'selected' : ''; ?>>
                            <?php echo sanitize($c['course_code'] . ' — ' . $c['course_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="date" class="form-label">Date</label>
                <input type="date" class="form-control" id="date" name="date"
                       value="<?php echo sanitize($selected_date); ?>" required>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-people me-1"></i>Load Students
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Attendance Form -->
<?php if ($show_form): ?>
    <?php if (empty($students)): ?>
        <div class="alert alert-info"><i class="bi bi-info-circle me-1"></i>No active enrollments found for this course.</div>
    <?php else: ?>
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    Mark Attendance — <?php echo format_date($selected_date); ?>
                    <span class="badge bg-secondary ms-2"><?php echo count($students); ?> students</span>
                </h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <?php csrf_field(); ?>
                    <input type="hidden" name="course_id" value="<?php echo $selected_course; ?>">
                    <input type="hidden" name="attendance_date" value="<?php echo sanitize($selected_date); ?>">

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Student</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $i => $s): ?>
                                    <tr>
                                        <td><?php echo $i + 1; ?></td>
                                        <td>
                                            <?php echo sanitize($s['first_name'] . ' ' . $s['last_name']); ?>
                                            <br><small class="text-muted"><?php echo sanitize($s['student_number']); ?></small>
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm" name="status[<?php echo $s['enrollment_id']; ?>]">
                                                <option value="present" <?php echo ($s['existing_status'] === 'present' || !$s['existing_status']) ? 'selected' : ''; ?>>Present</option>
                                                <option value="absent" <?php echo ($s['existing_status'] === 'absent') ? 'selected' : ''; ?>>Absent</option>
                                                <option value="late" <?php echo ($s['existing_status'] === 'late') ? 'selected' : ''; ?>>Late</option>
                                                <option value="excused" <?php echo ($s['existing_status'] === 'excused') ? 'selected' : ''; ?>>Excused</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm"
                                                   name="notes[<?php echo $s['enrollment_id']; ?>]"
                                                   value="<?php echo sanitize($s['existing_notes']); ?>"
                                                   placeholder="Optional notes">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <button type="submit" name="submit_attendance" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Save Attendance
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
