<?php
/**
 * functions.php — Shared Helper Functions
 *
 * Contains utility functions used throughout the application:
 * flash messages, redirects, sanitisation, formatting, and pagination.
 */

/**
 * Set a flash message in the session.
 * Flash messages are shown once and then automatically removed.
 *
 * @param string $key     The type of message: 'success', 'error', 'warning', 'info'
 * @param string $message The message text to display
 * @return void
 */
function flash($key, $message) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['flash'][$key] = $message;
}

/**
 * Get and remove a flash message from the session.
 *
 * @param string $key The type of message to retrieve
 * @return string|null The message text, or null if no message exists
 */
function get_flash($key) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['flash'][$key])) {
        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $message;
    }
    return null;
}

/**
 * Redirect the user to a different URL.
 *
 * @param string $url The URL to redirect to
 * @return void
 */
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

/**
 * Sanitise a string for safe output in HTML.
 * Converts special characters to HTML entities to prevent XSS attacks.
 *
 * @param string|null $string The string to sanitise
 * @return string The sanitised string
 */
function sanitize($string) {
    if ($string === null) {
        return '';
    }
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Format a date string into a readable format.
 *
 * @param string $date   The date string to format (e.g., '2025-03-15')
 * @param string $format The desired output format (default: 'd M Y')
 * @return string The formatted date string
 */
function format_date($date, $format = 'd M Y') {
    if (empty($date)) {
        return 'N/A';
    }
    $datetime = new DateTime($date);
    return $datetime->format($format);
}

/**
 * Format a datetime string into a readable format including time.
 *
 * @param string $datetime The datetime string to format
 * @param string $format   The desired output format (default: 'd M Y, h:i A')
 * @return string The formatted datetime string
 */
function format_datetime($datetime, $format = 'd M Y, h:i A') {
    if (empty($datetime)) {
        return 'N/A';
    }
    $dt = new DateTime($datetime);
    return $dt->format($format);
}

/**
 * Calculate grade letter from a percentage.
 * HD >= 85, D >= 75, C >= 65, P >= 50, F < 50
 *
 * @param float $marks_obtained The marks the student got
 * @param float $max_marks      The maximum possible marks
 * @return string The grade letter (HD, D, C, P, or F)
 */
function calculate_grade($marks_obtained, $max_marks) {
    if ($max_marks <= 0) {
        return 'F';
    }
    $percentage = ($marks_obtained / $max_marks) * 100;

    if ($percentage >= 85) return 'HD';
    if ($percentage >= 75) return 'D';
    if ($percentage >= 65) return 'C';
    if ($percentage >= 50) return 'P';
    return 'F';
}

/**
 * Build pagination data for list pages.
 * Returns an array with current page, total pages, offset, and limit.
 *
 * @param int $total_records The total number of records
 * @param int $per_page      Records per page (default: 10)
 * @param int $current_page  The current page number (default: 1)
 * @return array Pagination data: [current_page, total_pages, offset, per_page]
 */
function paginate($total_records, $per_page = 10, $current_page = 1) {
    // Calculate total number of pages
    $total_pages = max(1, ceil($total_records / $per_page));

    // Make sure current page is within valid range
    $current_page = max(1, min($current_page, $total_pages));

    // Calculate the offset for the SQL LIMIT clause
    $offset = ($current_page - 1) * $per_page;

    return [
        'current_page' => $current_page,
        'total_pages'  => $total_pages,
        'offset'       => $offset,
        'per_page'     => $per_page,
        'total_records' => $total_records,
    ];
}

/**
 * Render pagination HTML using Bootstrap 5 pagination component.
 *
 * @param array  $pagination The pagination data from paginate()
 * @param string $base_url   The base URL for pagination links (without page param)
 * @return string HTML string for the pagination component
 */
function render_pagination($pagination, $base_url) {
    if ($pagination['total_pages'] <= 1) {
        return '';
    }

    // Determine the separator for URL parameters
    $separator = (strpos($base_url, '?') !== false) ? '&' : '?';

    $html = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';

    // Previous button
    if ($pagination['current_page'] > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $base_url . $separator . 'page=' . ($pagination['current_page'] - 1) . '">&laquo; Previous</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">&laquo; Previous</span></li>';
    }

    // Page numbers
    for ($i = 1; $i <= $pagination['total_pages']; $i++) {
        if ($i == $pagination['current_page']) {
            $html .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
        } else {
            $html .= '<li class="page-item"><a class="page-link" href="' . $base_url . $separator . 'page=' . $i . '">' . $i . '</a></li>';
        }
    }

    // Next button
    if ($pagination['current_page'] < $pagination['total_pages']) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $base_url . $separator . 'page=' . ($pagination['current_page'] + 1) . '">Next &raquo;</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">Next &raquo;</span></li>';
    }

    $html .= '</ul></nav>';
    return $html;
}

/**
 * Get the display name for the current user based on their role.
 * Returns the first_name from the students or teachers table,
 * or the username if no profile record is found.
 *
 * @param array $user The user data from current_user()
 * @return string The display name
 */
function get_display_name($user) {
    if ($user['role'] === 'student') {
        $profile = Database::fetch_one("SELECT first_name, last_name FROM students WHERE user_id = ?", [$user['id']]);
    } elseif ($user['role'] === 'teacher') {
        $profile = Database::fetch_one("SELECT first_name, last_name FROM teachers WHERE user_id = ?", [$user['id']]);
    } else {
        return $user['username'];
    }

    if ($profile) {
        return $profile['first_name'] . ' ' . $profile['last_name'];
    }
    return $user['username'];
}
