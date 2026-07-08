<?php
/**
 * students/list.php — Student Management List Page
 *
 * Displays a searchable, sortable, and paginated list of all active students.
 * Admin-only page that allows viewing, editing, and deleting student records.
 *
 * GET Parameters:
 *  - search: Filter students by first_name, last_name, or student_number
 *  - sort: Column to sort by (student_number, first_name, last_name, email, phone, enrollment_year)
 *  - order: Sort direction (asc, desc)
 *  - page: Current page number for pagination
 */

// ============================================================
// SETUP & AUTHORIZATION
// ============================================================

// Set page title before including header
$page_title = 'Student Management';

// Include header (which also includes auth, db, functions, config, csrf)
require_once __DIR__ . '/../../includes/header.php';

// Require admin role for this page
require_role('admin');

// ============================================================
// RETRIEVE QUERY PARAMETERS
// ============================================================

// Get search parameter from GET request
$search = trim($_GET['search'] ?? '');

// Get sort parameter (validate to prevent SQL injection)
$allowed_sorts = ['student_number', 'first_name', 'last_name', 'email', 'phone', 'enrollment_year'];
$sort_input = $_GET['sort'] ?? 'student_number';
$sort = in_array($sort_input, $allowed_sorts) ? $sort_input : 'student_number';
$sort_alias = ($sort === 'email') ? 'u' : 's';

// Get sort order parameter (validate)
$order = (isset($_GET['order']) && strtolower($_GET['order']) === 'desc') ? 'DESC' : 'ASC';

// Get page number from GET request (default to 1)
$page = max(1, (int)($_GET['page'] ?? 1));

// ============================================================
// BUILD BASE QUERY
// ============================================================

// Base WHERE clause - only active students
$where = "WHERE u.is_active = 1";
$params = [];

// Add search filter if search term provided
if (!empty($search)) {
    $where .= " AND (s.first_name LIKE ? OR s.last_name LIKE ? OR s.student_number LIKE ?)";
    $search_term = "%{$search}%";
    $params = [$search_term, $search_term, $search_term];
}

// ============================================================
// COUNT TOTAL RECORDS
// ============================================================

// Count query to determine pagination
$count_sql = "SELECT COUNT(*) as total FROM students s
              JOIN users u ON s.user_id = u.id
              {$where}";
$count_result = Database::fetch_one($count_sql, $params);
$total_records = $count_result['total'] ?? 0;

// ============================================================
// PAGINATION
// ============================================================

// Calculate pagination data
$pagination = paginate($total_records, 10, $page);

// ============================================================
// FETCH PAGINATED STUDENT DATA
// ============================================================

// Data query with search/sort/pagination
$data_sql = "SELECT s.*, u.email, u.is_active
             FROM students s
             JOIN users u ON s.user_id = u.id
             {$where}
             ORDER BY {$sort_alias}.{$sort} {$order}
             LIMIT ? OFFSET ?";

// Append pagination params
$data_params = $params;
$data_params[] = $pagination['per_page'];
$data_params[] = $pagination['offset'];

// Fetch students
$students = Database::fetch_all($data_sql, $data_params);

// ============================================================
// BUILD SORT LINKS
// ============================================================

/**
 * Helper function to build sort link with current filters
 */
function build_sort_link($column, $current_sort, $current_order, $search_term) {
    $new_order = ($current_sort === $column && $current_order === 'ASC') ? 'DESC' : 'ASC';
    $params = "sort={$column}&order={$new_order}";
    if (!empty($search_term)) {
        $params .= "&search=" . urlencode($search_term);
    }
    return BASE_URL . "/students/list.php?{$params}";
}

// ============================================================
// BUILD PAGINATION BASE URL
// ============================================================

$pagination_params = "sort={$sort}&order={$order}";
if (!empty($search)) {
    $pagination_params .= "&search=" . urlencode($search);
}
$pagination_base_url = BASE_URL . "/students/list.php?" . $pagination_params;

?>

<!-- ============================================================ -->
<!-- PAGE HEADER WITH TITLE AND ADD BUTTON                        -->
<!-- ============================================================ -->
<div class="row mb-4">
    <div class="col-md-8">
        <h1 class="page-heading">Student Management</h1>
        <p class="text-muted">Total students: <strong><?php echo $total_records; ?></strong></p>
    </div>
    <div class="col-md-4 text-end">
        <a href="<?php echo BASE_URL; ?>/students/add.php" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Add Student
        </a>
    </div>
</div>

<!-- ============================================================ -->
<!-- SEARCH FORM                                                  -->
<!-- ============================================================ -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?php echo BASE_URL; ?>/students/list.php" class="row g-3">
            <!-- Search Input -->
            <div class="col-md-6">
                <label for="search-input" class="form-label">Search Students</label>
                <input
                    type="text"
                    id="search-input"
                    name="search"
                    class="form-control"
                    placeholder="Search by name or student number..."
                    value="<?php echo sanitize($search); ?>"
                >
            </div>

            <!-- Sort Dropdown -->
            <div class="col-md-3">
                <label for="sort-select" class="form-label">Sort By</label>
                <select id="sort-select" name="sort" class="form-select">
                    <option value="student_number" <?php echo $sort === 'student_number' ? 'selected' : ''; ?>>Student Number</option>
                    <option value="first_name" <?php echo $sort === 'first_name' ? 'selected' : ''; ?>>First Name</option>
                    <option value="last_name" <?php echo $sort === 'last_name' ? 'selected' : ''; ?>>Last Name</option>
                    <option value="email" <?php echo $sort === 'email' ? 'selected' : ''; ?>>Email</option>
                    <option value="phone" <?php echo $sort === 'phone' ? 'selected' : ''; ?>>Phone</option>
                    <option value="enrollment_year" <?php echo $sort === 'enrollment_year' ? 'selected' : ''; ?>>Enrollment Year</option>
                </select>
            </div>

            <!-- Submit Button -->
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="bi bi-search me-2"></i>Search
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================ -->
<!-- STUDENTS TABLE OR EMPTY STATE                               -->
<!-- ============================================================ -->

<?php if (!empty($students)): ?>
    <!-- Table with student data -->
    <div class="table-responsive">
        <table class="table table-hover table-striped">
            <!-- Table Header -->
            <thead class="table-dark">
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 15%;">
                        <a href="<?php echo build_sort_link('student_number', $sort, $order, $search); ?>" class="text-white text-decoration-none">
                            Student Number
                            <?php if ($sort === 'student_number'): ?>
                                <i class="bi bi-arrow-<?php echo $order === 'ASC' ? 'up' : 'down'; ?>"></i>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th style="width: 20%;">
                        <a href="<?php echo build_sort_link('first_name', $sort, $order, $search); ?>" class="text-white text-decoration-none">
                            Name
                            <?php if ($sort === 'first_name'): ?>
                                <i class="bi bi-arrow-<?php echo $order === 'ASC' ? 'up' : 'down'; ?>"></i>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th style="width: 20%;">
                        <a href="<?php echo build_sort_link('email', $sort, $order, $search); ?>" class="text-white text-decoration-none">
                            Email
                            <?php if ($sort === 'email'): ?>
                                <i class="bi bi-arrow-<?php echo $order === 'ASC' ? 'up' : 'down'; ?>"></i>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th style="width: 15%;">
                        <a href="<?php echo build_sort_link('phone', $sort, $order, $search); ?>" class="text-white text-decoration-none">
                            Phone
                            <?php if ($sort === 'phone'): ?>
                                <i class="bi bi-arrow-<?php echo $order === 'ASC' ? 'up' : 'down'; ?>"></i>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th style="width: 12%;">
                        <a href="<?php echo build_sort_link('enrollment_year', $sort, $order, $search); ?>" class="text-white text-decoration-none">
                            Year
                            <?php if ($sort === 'enrollment_year'): ?>
                                <i class="bi bi-arrow-<?php echo $order === 'ASC' ? 'up' : 'down'; ?>"></i>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th style="width: 13%;">Actions</th>
                </tr>
            </thead>

            <!-- Table Body -->
            <tbody>
                <?php
                // Counter for row numbering
                $row_number = $pagination['offset'] + 1;

                foreach ($students as $student):
                ?>
                    <tr>
                        <!-- Row Number -->
                        <td><?php echo $row_number; ?></td>

                        <!-- Student Number -->
                        <td>
                            <strong><?php echo sanitize($student['student_number']); ?></strong>
                        </td>

                        <!-- Name (First and Last) -->
                        <td>
                            <?php echo sanitize($student['first_name'] . ' ' . $student['last_name']); ?>
                        </td>

                        <!-- Email -->
                        <td>
                            <a href="mailto:<?php echo sanitize($student['email']); ?>">
                                <?php echo sanitize($student['email']); ?>
                            </a>
                        </td>

                        <!-- Phone -->
                        <td>
                            <?php echo sanitize($student['phone'] ?: 'N/A'); ?>
                        </td>

                        <!-- Enrollment Year -->
                        <td>
                            <?php echo sanitize($student['enrollment_year']); ?>
                        </td>

                        <!-- Actions (View, Edit, Delete) -->
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <!-- View Button -->
                                <a
                                    href="<?php echo BASE_URL; ?>/students/view.php?id=<?php echo $student['id']; ?>"
                                    class="btn btn-info btn-sm"
                                    title="View student details"
                                >
                                    <i class="bi bi-eye"></i> View
                                </a>

                                <!-- Edit Button -->
                                <a
                                    href="<?php echo BASE_URL; ?>/students/edit.php?id=<?php echo $student['id']; ?>"
                                    class="btn btn-warning btn-sm"
                                    title="Edit student information"
                                >
                                    <i class="bi bi-pencil"></i> Edit
                                </a>

                                <!-- Delete Button with Confirmation -->
                                <a
                                    href="<?php echo BASE_URL; ?>/students/delete.php?id=<?php echo $student['id']; ?>"
                                    class="btn btn-danger btn-sm"
                                    title="Delete student record"
                                    data-confirm="Are you sure you want to delete this student? This action cannot be undone."
                                >
                                    <i class="bi bi-trash"></i> Delete
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php
                    $row_number++;
                endforeach;
                ?>
            </tbody>
        </table>
    </div>

    <!-- ============================================================ -->
    <!-- PAGINATION                                                   -->
    <!-- ============================================================ -->
    <?php echo render_pagination($pagination, $pagination_base_url); ?>

<?php else: ?>
    <!-- Empty State Message -->
    <div class="alert alert-info text-center py-5">
        <i class="bi bi-inbox" style="font-size: 3rem;"></i>
        <h4 class="mt-3">No Students Found</h4>
        <p class="text-muted mb-3">
            <?php if (!empty($search)): ?>
                No students match your search criteria. Try adjusting your filters.
            <?php else: ?>
                There are no active students in the system yet. Click the button below to add one.
            <?php endif; ?>
        </p>
        <a href="<?php echo BASE_URL; ?>/students/add.php" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Add First Student
        </a>
    </div>
<?php endif; ?>

<?php
// ============================================================
// INCLUDE FOOTER
// ============================================================
require_once __DIR__ . '/../../includes/footer.php';
?>
