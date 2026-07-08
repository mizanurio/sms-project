<?php
/**
 * footer.php — Shared Page Footer
 *
 * Included at the bottom of every page. Provides:
 * - Footer with copyright notice
 * - Bootstrap 5 JavaScript bundle (includes Popper)
 * - Custom JavaScript file
 * - Closing HTML tags
 */
?>

</main>
<!-- End of main content area -->

<!-- ============================================================ -->
<!-- FOOTER                                                        -->
<!-- ============================================================ -->
<footer class="bg-dark text-light text-center py-3 mt-auto">
    <div class="container">
        <p class="mb-0">
            &copy; <?php echo date('Y'); ?> Student Management System &mdash;
            Developed by Shafayot | Torrens University Australia
        </p>
    </div>
</footer>

<!-- Bootstrap 5 JavaScript Bundle (includes Popper for dropdowns) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JavaScript -->
<script src="<?php echo BASE_URL; ?>/../assets/js/main.js"></script>

</body>
</html>
