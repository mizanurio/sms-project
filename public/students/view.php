<?php
/**
 * students/view.php — View Student Profile & Details
 *
 * Displays complete student information including personal details,
 * account information, and enrolled courses. Admin-only page.
 *
 * GET Parameters:
 *  - id: Student ID (required)
 */

// ============================================================
// SETUP & AUTHORIZATION
// ============================================================

// Set page title before including header
$page_title = 'View Student Profile';

// Include header (which includes auth, db, functions, config, csrf)
require_once __DIR__ . '/../../includes/header.php';

// Require admin role for this page
require_role('admin');

// ============================================================
// RETRIEVE STUDENT ID FROM QUERY PARAMETER
// ============================================================

// Get student ID from GET request
$student_id = (int)($_GET['id'] ?? 0);

// Validate that a valid ID was provided
if (empty($student_id)) {
    flash('error', 'Invalid student ID provided.');
    redirect(BASE_URL . '/students/list.php');
    exit;
}

// ============================================================
// FETCH STUDENT DATA
// ============================================================

// Query to fetch student with associated user data
$student = Database::fetch_one(
    "SELECT s.*, u.email, u.username, u.created_at as account_created
     FROM students s
     JOIN users u ON s.user_id = u.id
     WHERE s.id = ?",
    [$student_id]
);

// Check if student exists
if (!$student) {
    flash('error', 'Student not found.');
    redirect(BASE_URL . '/students/list.php');
    exit;
}

// ============================================================
// FETCH ENROLLED COURSES
// ============================================================

// Query to fetch all courses the student is enrolled in
$enrolled_courses = Database::fetch_all(
    "SELECT c.id, c.course_code, c.course_name, e.enrollment_date, e.status, e.id as enrollment_id
     FROM enrollments e
     JOIN courses c ON e.course_id = c.id
     WHERE e.student_id = ?
     ORDER BY e.enrollment_date DESC",
    [$student_id]
);

?>

<!-- ============================================================ -->
<!-- PAGE HEADER WITH BACK AND EDIT BUTTONS                      -->
<!-- ============================================================ -->
<div class="row mb-4">
    <div class="col-md-8">
        <h1 class="page-heading">Student Profile</h1>
        <p class="text-muted">View and manage student information</p>
    </div>
    <div class="col-md-4 text-end">
        <div class="btn-group" role="group">
            <!-- Back Button -->
            <a href="<?php echo BASE_URL; ?>/students/list.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to List
            </a>

            <!-- Edit Button -->
            <a href="<?php echo BASE_URL; ?>/students/edit.php?id=<?php echo $student['id']; ?>" class="btn btn-warning">
                <i class="bi bi-pencil me-2"></i>Edit Student
            </a>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- STUDENT BASIC INFORMATION CARD                              -->
<!-- ============================================================ -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-person-badge me-2"></i>Basic Information
                </h5>
            </div>
            <div class="card-body">
                <!-- Student Number -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Student Number</h6>
                        <p class="mb-0">
                            <strong><?php echo sanitize($student['student_number']); ?></strong>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Enrollment Year</h6>
                        <p class="mb-0">
                            <strong><?php echo sanitize($student['enrollment_year']); ?></strong>
                        </p>
                    </div>
                </div>

                <!-- Full Name -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">First Name</h6>
                        <p class="mb-0">
                            <strong><?php echo sanitize($student['first_name']); ?></strong>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Last Name</h6>
                        <p class="mb-0">
                            <strong><?php echo sanitize($student['last_name']); ?></strong>
                        </p>
                    </div>
                </div>

                <!-- Date of Birth and Gender -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Date of Birth</h6>
                        <p class="mb-0">
                            <strong>
                                <?php
                                if (!empty($student['date_of_birth'])) {
                                    echo sanitize(format_date($student['date_of_birth']));
                                } else {
                                    echo 'Not provided';
                                }
                                ?>
                            </strong>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Gender</h6>
                        <p class="mb-0">
                            <strong>
                                <?php echo !empty($student['gender']) ? sanitize($student['gender']) : 'Not provided'; ?>
                            </strong>
                        </p>
                    </div>
                </div>

                <!-- Phone and Address -->
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Phone</h6>
                        <p class="mb-0">
                            <strong>
                                <?php
                                if (!empty($student['phone'])) {
                                    echo '<a href="tel:' . sanitize($student['phone']) . '">' . sanitize($student['phone']) . '</a>';
                                } else {
                                    echo 'Not provided';
                                }
                                ?>
                            </strong>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Address</h6>
                        <p class="mb-0">
                            <strong>
                                <?php echo !empty($student['address']) ? sanitize($student['address']) : 'Not provided'; ?>
                            </strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- ACCOUNT INFORMATION CARD (SIDEBAR)                          -->
    <!-- ============================================================ -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="bi bi-shield-lock me-2"></i>Account Details
                </h5>
            </div>
            <div class="card-body">
                <!-- Username -->
                <div class="mb-4">
                    <h6 class="text-muted mb-1">Username</h6>
                    <p class="mb-0">
                        <strong><?php echo sanitize($student['username']); ?></strong>
                    </p>
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <h6 class="text-muted mb-1">Email Address</h6>
                    <p class="mb-0">
                        <strong>
                            <a href="mailto:<?php echo sanitize($student['email']); ?>">
                                <?php echo sanitize($student['email']); ?>
                            </a>
                        </strong>
                    </p>
                </div>

                <!-- Account Created -->
                <div class="mb-4">
                    <h6 class="text-muted mb-1">Account Created</h6>
                    <p class="mb-0">
                        <strong><?php echo sanitize(format_datetime($student['account_created'])); ?></strong>
                    </p>
                </div>

                <!-- Status Badge -->
                <div>
                    <h6 class="text-muted mb-1">Account Status</h6>
                    <p class="mb-0">
                        <?php if ($student['is_active']): ?>
                            <span class="badge bg-success">
                                <i class="bi bi-check-circle me-1"></i>Active
                            </span>
                        <?php else: ?>
                            <span class="badge bg-danger">
                                <i class="bi bi-x-circle me-1"></i>Inactive
                            </span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- ENROLLED COURSES SECTION                                     -->
<!-- ============================================================ -->
<div class="card">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">
            <i class="bi bi-book me-2"></i>Enrolled Courses
        </h5>
    </div>
    <div class="card-body">
        <?php if (!empty($enrolled_courses)): ?>
            <!-- Courses Table -->
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <!-- Table Header -->
                    <thead class="table-light">
                        <tr>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Enrollment Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <!-- Table Body -->
                    <tbody>
                        <?php foreach ($enrolled_courses as $course): ?>
                            <tr>
                                <!-- Course Code -->
                                <td>
                                    <strong><?php echo sanitize($course['course_code']); ?></strong>
                                </td>

                                <!-- Course Name -->
                                <td>
                                    <?php echo sanitize($course['course_name']); ?>
                                </td>

                                <!-- Enrollment Date -->
                                <td>
                                    <?php echo sanitize(format_date($course['enrollment_date'])); ?>
                                </td>

                                <!-- Status Badge -->
                                <td>
                                    <?php
                                    // Determine status badge color based on enrollment status
                                    $status_color = match($course['status']) {
                                        'active' => 'success',
                                        'completed' => 'primary',
                                        'withdrawn' => 'warning',
                                        'deferred' => 'secondary',
                                        default => 'secondary'
                                    };
                                    ?>
                                    <span class="badge bg-<?php echo $status_color; ?>">
                                        <?php echo ucfirst(sanitize($course['status'])); ?>
                                    </span>
                                </td>

                                <!-- Actions -->
                                <td>
                                    <a
                                        href="<?php echo BASE_URL; ?>/courses/view.php?id=<?php echo $course['id']; ?>"
                                        class="btn btn-sm btn-outline-primary"
                                        title="View course details"
                                    >
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php else: ?>
            <!-- No Enrollments Message -->
            <div class="alert alert-info mb-0">
                <i class="bi bi-info-circle me-2"></i>
                This student is not currently enrolled in any courses.
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- ============================================================ -->
<!-- ADDITIONAL ACTIONS SECTION                                   -->
<!-- ============================================================ -->
<div class="mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex gap-2 justify-content-between">
                <!-- Back Button -->
                <a href="<?php echo BASE_URL; ?>/students/list.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Student List
                </a>

                <!-- Edit Button -->
                <div class="d-flex gap-2">
                    <a href="<?php echo BASE_URL; ?>/students/edit.php?id=<?php echo $student['id']; ?>" class="btn btn-warning">
                        <i class="bi bi-pencil me-2"></i>Edit Student Information
                    </a>

                    <!-- Delete Button with Confirmation -->
                    <a
                        href="<?php echo BASE_URL; ?>/students/delete.php?id=<?php echo $student['id']; ?>"
                        class="btn btn-danger"
                        title="Delete student record"
                        data-confirm="Are you sure you want to delete this student record? This action cannot be undone."
                    >
                        <i class="bi bi-trash me-2"></i>Delete Student
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// ============================================================
// INCLUDE FOOTER
// ============================================================
require_once __DIR__ . '/../../includes/footer.php';
?>
