<?php
/**
 * list.php — Teacher List (Admin Only)
 *
 * Displays all active teachers with search and pagination.
 */

$page_title = 'Teachers';
require_once __DIR__ . '/../../includes/header.php';
require_role('admin');

// Get parameters
$search = trim($_GET['search'] ?? '');
$current_page = max(1, intval($_GET['page'] ?? 1));

// Build WHERE
$where = "WHERE u.is_active = 1";
$params = [];
if (!empty($search)) {
    $where .= " AND (t.first_name LIKE ? OR t.last_name LIKE ? OR t.employee_number LIKE ?)";
    $like = '%' . $search . '%';
    $params = [$like, $like, $like];
}

// Count
$total = Database::fetch_one("SELECT COUNT(*) as count FROM teachers t JOIN users u ON t.user_id = u.id $where", $params)['count'];
$pagination = paginate($total, 10, $current_page);

// Fetch
$sql = "SELECT t.*, u.email FROM teachers t JOIN users u ON t.user_id = u.id $where
        ORDER BY t.first_name ASC
        LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}";
$teachers = Database::fetch_all($sql, $params);
$base_url = BASE_URL . '/teachers/list.php' . (!empty($search) ? '?search=' . urlencode($search) : '');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-person-badge me-2"></i>Teacher Management</h1>
    <a href="<?php echo BASE_URL; ?>/teachers/add.php" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Add Teacher
    </a>
</div>

<!-- Search -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control"
                   placeholder="Search by name or employee number..."
                   value="<?php echo sanitize($search); ?>">
            <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search me-1"></i>Search</button>
            <?php if (!empty($search)): ?>
                <a href="<?php echo BASE_URL; ?>/teachers/list.php" class="btn btn-outline-secondary">Clear</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<p class="text-muted mb-3">Showing <?php echo $total; ?> teacher(s)</p>

<?php if (!empty($teachers)): ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>#</th><th>Employee No.</th><th>Name</th><th>Email</th>
                    <th>Department</th><th>Phone</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($teachers as $i => $t): ?>
                    <tr>
                        <td><?php echo $pagination['offset'] + $i + 1; ?></td>
                        <td><span class="badge bg-success"><?php echo sanitize($t['employee_number']); ?></span></td>
                        <td><?php echo sanitize($t['first_name'] . ' ' . $t['last_name']); ?></td>
                        <td><?php echo sanitize($t['email']); ?></td>
                        <td><?php echo sanitize($t['department']); ?></td>
                        <td><?php echo sanitize($t['phone'] ?? '—'); ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo BASE_URL; ?>/teachers/view.php?id=<?php echo $t['id']; ?>" class="btn btn-info" title="View"><i class="bi bi-eye"></i></a>
                                <a href="<?php echo BASE_URL; ?>/teachers/edit.php?id=<?php echo $t['id']; ?>" class="btn btn-warning" title="Edit"><i class="bi bi-pencil"></i></a>
                                <a href="<?php echo BASE_URL; ?>/teachers/delete.php?id=<?php echo $t['id']; ?>" class="btn btn-danger" title="Delete"><i class="bi bi-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php echo render_pagination($pagination, $base_url); ?>
<?php else: ?>
    <div class="alert alert-info"><i class="bi bi-info-circle me-1"></i>No teachers found.</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
