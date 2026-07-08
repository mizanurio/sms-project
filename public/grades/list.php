<?php
/**
 * list.php — Grade List (Teacher and Admin)
 *
 * Teachers see only their own course enrollments.
 * Admin sees all enrollments across all courses.
 * Allows filtering by course.
 */

$page_title = 'Grades';
require_once __DIR__ . '/../../includes/header.php';
require_role(['admin', 'teacher']);

$user = current_user();
$filter_course = intval($_GET['course_id'] ?? 0);
$current_page = max(1, intval($_GET['page'] ?? 1));

// Build course list based on role
if ($user['role'] === 'teacher') {
    $teacher = Database::fetch_one("SELECT id FROM teachers WHERE user_id = ?", [$user['id']]);
    if (!$teacher) {
        flash('error', 'Teacher profile not found.');
        redirect(BASE_URL . '/dashboard.php');
    }
    $my_courses = Database::fetch_all(
        "SELECT id, course_code, course_name FROM courses WHERE teacher_id = ? ORDER BY course_code",
        [$teacher['id']]
    );
    $course_ids = array_column($my_courses, 'id');
} else {
    $my_courses = Database::fetch_all("SELECT id, course_code, course_name FROM courses ORDER BY course_code");
    $course_ids = array_column($my_courses, 'id');
}

// Build WHERE for grades query
$where_parts = [];
$params = [];

if (!empty($course_ids)) {
    $placeholders = implode(',', array_fill(0, count($course_ids), '?'));
    $where_parts[] = "e.course_id IN ($placeholders)";
    $params = $course_ids;
} else {
    // No courses — show nothing
    $where_parts[] = "1 = 0";
}

if ($filter_course > 0) {
    $where_parts[] = "e.course_id = ?";
    $params[] = $filter_course;
}

$where = 'WHERE ' . implode(' AND ', $where_parts);

// Count and paginate
$total = Database::fetch_one("SELECT COUNT(*) as count FROM grades g JOIN enrollments e ON g.enrollment_id = e.id $where", $params)['count'];
$pagination = paginate($total, 10, $current_page);

// Fetch grades
$sql = "SELECT g.*, e.student_id, e.course_id,
               s.first_name as s_first, s.last_name as s_last, s.student_number,
               c.course_code, c.course_name
        FROM grades g
        JOIN enrollments e ON g.enrollment_id = e.id
        JOIN students s ON e.student_id = s.id
        JOIN courses c ON e.course_id = c.id
        $where
        ORDER BY g.recorded_at DESC
        LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}";
$grades = Database::fetch_all($sql, $params);

$base_url = BASE_URL . '/grades/list.php' . ($filter_course ? '?course_id=' . $filter_course : '');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-award me-2"></i>Grade Management</h1>
    <a href="<?php echo BASE_URL; ?>/grades/add.php" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Add Grade
    </a>
</div>

<!-- Filter by course -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-6">
                <label class="form-label">Filter by Course</label>
                <select name="course_id" class="form-select">
                    <option value="">All My Courses</option>
                    <?php foreach ($my_courses as $c): ?>
                        <option value="<?php echo $c['id']; ?>" <?php echo ($filter_course == $c['id']) ? 'selected' : ''; ?>>
                            <?php echo sanitize($c['course_code'] . ' — ' . $c['course_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
            </div>
            <div class="col-md-3">
                <a href="<?php echo BASE_URL; ?>/grades/list.php" class="btn btn-outline-secondary w-100">Clear</a>
            </div>
        </form>
    </div>
</div>

<p class="text-muted mb-3">Showing <?php echo $total; ?> grade record(s)</p>

<?php if (!empty($grades)): ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Student</th>
                    <th>Course</th>
                    <th>Assessment</th>
                    <th>Marks</th>
                    <th>Grade</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($grades as $g): ?>
                    <tr>
                        <td>
                            <?php echo sanitize($g['s_first'] . ' ' . $g['s_last']); ?>
                            <br><small class="text-muted"><?php echo sanitize($g['student_number']); ?></small>
                        </td>
                        <td><?php echo sanitize($g['course_code']); ?></td>
                        <td><?php echo sanitize($g['assessment_name']); ?></td>
                        <td><?php echo $g['marks_obtained']; ?> / <?php echo $g['max_marks']; ?></td>
                        <td>
                            <?php
                            $gc = match($g['grade_letter']) {
                                'HD' => 'success', 'D' => 'info', 'C' => 'primary',
                                'P' => 'warning', 'F' => 'danger', default => 'secondary'
                            };
                            ?>
                            <span class="badge bg-<?php echo $gc; ?>"><?php echo sanitize($g['grade_letter']); ?></span>
                        </td>
                        <td><?php echo format_date($g['recorded_at']); ?></td>
                        <td>
                            <a href="<?php echo BASE_URL; ?>/grades/edit.php?id=<?php echo $g['id']; ?>"
                               class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php echo render_pagination($pagination, $base_url); ?>
<?php else: ?>
    <div class="alert alert-info"><i class="bi bi-info-circle me-1"></i>No grade records found.</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
