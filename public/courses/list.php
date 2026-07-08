<?php
/**
 * list.php — Course List (Admin Only)
 *
 * Displays all courses with search and pagination.
 * Shows teacher assignment and action buttons.
 */

$page_title = 'Courses';
require_once __DIR__ . '/../../includes/header.php';
require_role('admin');

// Get search parameter from URL
$search = trim($_GET['search'] ?? '');
$current_page = max(1, intval($_GET['page'] ?? 1));

// Build the WHERE clause for search
$where = "";
$params = [];
if (!empty($search)) {
    $where = "WHERE c.course_code LIKE ? OR c.course_name LIKE ?";
    $like = '%' . $search . '%';
    $params = [$like, $like];
}

// Count total records for pagination
$count_sql = "SELECT COUNT(*) as count FROM courses c $where";
$total_records = Database::fetch_one($count_sql, $params)['count'];
$pagination = paginate($total_records, 10, $current_page);

// Fetch courses with teacher names
$sql = "SELECT c.*, t.first_name as teacher_first, t.last_name as teacher_last
        FROM courses c
        LEFT JOIN teachers t ON c.teacher_id = t.id
        $where
        ORDER BY c.course_code ASC
        LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}";
$courses = Database::fetch_all($sql, $params);

// Build base URL for pagination links
$base_url = BASE_URL . '/courses/list.php' . (!empty($search) ? '?search=' . urlencode($search) : '');
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-book me-2"></i>Course Management</h1>
    <a href="<?php echo BASE_URL; ?>/courses/add.php" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Add Course
    </a>
</div>

<!-- Search Bar -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control"
                   placeholder="Search by course code or name..."
                   value="<?php echo sanitize($search); ?>">
            <button type="submit" class="btn btn-outline-primary">
                <i class="bi bi-search me-1"></i>Search
            </button>
            <?php if (!empty($search)): ?>
                <a href="<?php echo BASE_URL; ?>/courses/list.php" class="btn btn-outline-secondary">Clear</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Results count -->
<p class="text-muted mb-3">Showing <?php echo $total_records; ?> course(s)</p>

<!-- Courses Table -->
<?php if (!empty($courses)): ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Credits</th>
                    <th>Teacher</th>
                    <th>Semester / Year</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($courses as $course): ?>
                    <tr>
                        <td><span class="badge bg-info"><?php echo sanitize($course['course_code']); ?></span></td>
                        <td><?php echo sanitize($course['course_name']); ?></td>
                        <td><?php echo intval($course['credits']); ?></td>
                        <td>
                            <?php if ($course['teacher_first']): ?>
                                <?php echo sanitize($course['teacher_first'] . ' ' . $course['teacher_last']); ?>
                            <?php else: ?>
                                <span class="text-muted">Unassigned</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $sem = ($course['semester'] === 'summer') ? 'Summer' : 'Sem ' . $course['semester'];
                            echo sanitize($sem . ', ' . $course['year']);
                            ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo BASE_URL; ?>/courses/view.php?id=<?php echo $course['id']; ?>" class="btn btn-info" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?php echo BASE_URL; ?>/courses/edit.php?id=<?php echo $course['id']; ?>" class="btn btn-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="<?php echo BASE_URL; ?>/courses/delete.php?id=<?php echo $course['id']; ?>" class="btn btn-danger" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php echo render_pagination($pagination, $base_url); ?>
<?php else: ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-1"></i>No courses found.
        <?php if (!empty($search)): ?>
            <a href="<?php echo BASE_URL; ?>/courses/list.php">Clear search</a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
