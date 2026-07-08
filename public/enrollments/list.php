<?php
/**
 * list.php — Enrollment List (Admin Only)
 *
 * Displays all enrollments with filters by course or student.
 * Allows dropping enrollments (changing status to 'dropped').
 */

$page_title = 'Enrollments';
require_once __DIR__ . '/../../includes/header.php';
require_role('admin');

// Get filter parameters
$filter_course  = intval($_GET['course_id'] ?? 0);
$filter_student = intval($_GET['student_id'] ?? 0);
$filter_status  = $_GET['status'] ?? '';
$current_page   = max(1, intval($_GET['page'] ?? 1));

// Build WHERE clause
$where_parts = [];
$params = [];

if ($filter_course > 0) {
    $where_parts[] = "e.course_id = ?";
    $params[] = $filter_course;
}
if ($filter_student > 0) {
    $where_parts[] = "e.student_id = ?";
    $params[] = $filter_student;
}
if (!empty($filter_status) && in_array($filter_status, ['active', 'completed', 'dropped'])) {
    $where_parts[] = "e.status = ?";
    $params[] = $filter_status;
}

$where = !empty($where_parts) ? 'WHERE ' . implode(' AND ', $where_parts) : '';

// Count total
$total = Database::fetch_one("SELECT COUNT(*) as count FROM enrollments e $where", $params)['count'];
$pagination = paginate($total, 10, $current_page);

// Fetch enrollments
$sql = "SELECT e.*, s.student_number, s.first_name as s_first, s.last_name as s_last,
               c.course_code, c.course_name
        FROM enrollments e
        JOIN students s ON e.student_id = s.id
        JOIN courses c ON e.course_id = c.id
        $where
        ORDER BY e.enrollment_date DESC
        LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}";
$enrollments = Database::fetch_all($sql, $params);

// Fetch courses and students for filter dropdowns
$courses = Database::fetch_all("SELECT id, course_code, course_name FROM courses ORDER BY course_code");
$students = Database::fetch_all(
    "SELECT s.id, s.student_number, s.first_name, s.last_name
     FROM students s JOIN users u ON s.user_id = u.id WHERE u.is_active = 1 ORDER BY s.first_name"
);

// Build base URL for pagination
$url_params = [];
if ($filter_course) $url_params[] = 'course_id=' . $filter_course;
if ($filter_student) $url_params[] = 'student_id=' . $filter_student;
if ($filter_status) $url_params[] = 'status=' . urlencode($filter_status);
$base_url = BASE_URL . '/enrollments/list.php' . (!empty($url_params) ? '?' . implode('&', $url_params) : '');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-card-checklist me-2"></i>Enrollment Management</h1>
    <a href="<?php echo BASE_URL; ?>/enrollments/add.php" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Add Enrollment
    </a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Filter by Course</label>
                <select name="course_id" class="form-select">
                    <option value="">All Courses</option>
                    <?php foreach ($courses as $c): ?>
                        <option value="<?php echo $c['id']; ?>" <?php echo ($filter_course == $c['id']) ? 'selected' : ''; ?>>
                            <?php echo sanitize($c['course_code'] . ' — ' . $c['course_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Filter by Student</label>
                <select name="student_id" class="form-select">
                    <option value="">All Students</option>
                    <?php foreach ($students as $s): ?>
                        <option value="<?php echo $s['id']; ?>" <?php echo ($filter_student == $s['id']) ? 'selected' : ''; ?>>
                            <?php echo sanitize($s['first_name'] . ' ' . $s['last_name'] . ' (' . $s['student_number'] . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="active" <?php echo ($filter_status === 'active') ? 'selected' : ''; ?>>Active</option>
                    <option value="completed" <?php echo ($filter_status === 'completed') ? 'selected' : ''; ?>>Completed</option>
                    <option value="dropped" <?php echo ($filter_status === 'dropped') ? 'selected' : ''; ?>>Dropped</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
            </div>
            <div class="col-md-2">
                <a href="<?php echo BASE_URL; ?>/enrollments/list.php" class="btn btn-outline-secondary w-100">Clear</a>
            </div>
        </form>
    </div>
</div>

<p class="text-muted mb-3">Showing <?php echo $total; ?> enrollment(s)</p>

<?php if (!empty($enrollments)): ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Student</th>
                    <th>Course</th>
                    <th>Enrollment Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($enrollments as $i => $e): ?>
                    <tr>
                        <td><?php echo $pagination['offset'] + $i + 1; ?></td>
                        <td>
                            <?php echo sanitize($e['s_first'] . ' ' . $e['s_last']); ?>
                            <br><small class="text-muted"><?php echo sanitize($e['student_number']); ?></small>
                        </td>
                        <td>
                            <?php echo sanitize($e['course_code'] . ' — ' . $e['course_name']); ?>
                        </td>
                        <td><?php echo format_date($e['enrollment_date']); ?></td>
                        <td>
                            <?php
                            $badge = match($e['status']) {
                                'active' => 'success', 'completed' => 'secondary', 'dropped' => 'danger', default => 'warning'
                            };
                            ?>
                            <span class="badge bg-<?php echo $badge; ?>"><?php echo ucfirst(sanitize($e['status'])); ?></span>
                        </td>
                        <td>
                            <?php if ($e['status'] === 'active'): ?>
                                <a href="<?php echo BASE_URL; ?>/enrollments/delete.php?id=<?php echo $e['id']; ?>"
                                   class="btn btn-sm btn-outline-danger" title="Drop">
                                    <i class="bi bi-x-circle"></i> Drop
                                </a>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php echo render_pagination($pagination, $base_url); ?>
<?php else: ?>
    <div class="alert alert-info"><i class="bi bi-info-circle me-1"></i>No enrollments found.</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
