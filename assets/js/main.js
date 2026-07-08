/**
 * main.js — Application JavaScript
 *
 * Contains all client-side JavaScript functionality for the SMS application.
 * Vanilla JavaScript with no external dependencies (except Bootstrap for alerts).
 */

// --- Execute when DOM is fully loaded ---
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all custom functions
    initializeAutoDissmissAlerts();
    initializeDeleteConfirmation();
    initializeFormLoadingState();
    initializeSessionTimeout();
});

/**
 * Auto-dismiss Bootstrap alerts after 5 seconds
 *
 * Finds all dismissible alert elements (.alert-dismissible) and automatically
 * fades them out after 5 seconds, then removes them from the DOM.
 */
function initializeAutoDissmissAlerts() {
    // Get all dismissible alert elements from the page
    const alerts = document.querySelectorAll('.alert-dismissible');

    // Loop through each alert
    alerts.forEach(function(alert) {
        // Set a timer for 5 seconds (5000 milliseconds)
        setTimeout(function() {
            // Add Bootstrap fade class to animate the disappearance
            alert.classList.add('fade');

            // Wait for fade animation to complete, then remove from DOM
            setTimeout(function() {
                alert.remove();
            }, 150);
        }, 5000);
    });
}

/**
 * Delete confirmation handler
 *
 * Listens for clicks on elements with data-confirm attribute.
 * Shows a browser confirmation dialog before allowing the action to proceed.
 * If user cancels the dialog, the default action is prevented.
 *
 * Usage: <a href="/delete" class="btn btn-danger" data-confirm="Are you sure?">Delete</a>
 */
function initializeDeleteConfirmation() {
    // Get all elements that require confirmation before action
    const confirmElements = document.querySelectorAll('[data-confirm]');

    // Loop through each element that needs confirmation
    confirmElements.forEach(function(element) {
        // Add click event listener to each element
        element.addEventListener('click', function(event) {
            // Get the confirmation message from the data-confirm attribute
            const confirmMessage = this.getAttribute('data-confirm');

            // Show browser confirm dialog with the message
            const isConfirmed = confirm(confirmMessage);

            // If user clicked "Cancel" in the dialog, prevent the action
            if (!isConfirmed) {
                event.preventDefault();
            }
        });
    });
}

/**
 * Form submit loading state
 *
 * When a form is submitted, this function:
 * 1. Finds the submit button
 * 2. Disables the button to prevent double-submission
 * 3. Changes the button text to "Processing..."
 * 4. Adds a loading class for visual feedback
 *
 * Usage: Any form on the page will automatically get this behavior
 */
function initializeFormLoadingState() {
    // Get all form elements on the page
    const forms = document.querySelectorAll('form');

    // Loop through each form
    forms.forEach(function(form) {
        // Add submit event listener to the form
        form.addEventListener('submit', function(event) {
            // Find the submit button within this form
            const submitButton = this.querySelector('button[type="submit"]');

            // Check if a submit button was found
            if (submitButton) {
                // Store the original button text for potential future use
                const originalText = submitButton.textContent;

                // Disable the button to prevent multiple submissions
                submitButton.disabled = true;

                // Add the btn-loading class for styling (opacity reduction, cursor change)
                submitButton.classList.add('btn-loading');

                // Change the button text to indicate processing
                submitButton.textContent = 'Processing...';

                // Add a spinner icon if desired (uncomment to use)
                // submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
            }
        });
    });
}

/**
 * Session timeout warning
 *
 * Displays an alert warning the user about session timeout after 25 minutes.
 * This gives the user 5 minutes warning before the actual 30-minute timeout.
 * The warning appears only once per session.
 *
 * Configuration: SESSION_TIMEOUT is defined in config.php (default: 1800 seconds / 30 minutes)
 */
function initializeSessionTimeout() {
    // Session timeout is 30 minutes (1800 seconds)
    // We warn the user at 25 minutes (1500 seconds)
    const sessionTimeoutSeconds = 1800;
    const warningTimeSeconds = 1500;

    // Calculate warning time in milliseconds (1500 seconds = 1500000 milliseconds)
    const warningTimeMs = warningTimeSeconds * 1000;

    // Set a timer to show the warning after 25 minutes
    setTimeout(function() {
        // Create the warning message
        const warningMessage = 'Your session will expire in 5 minutes due to inactivity. ' +
                               'Click anywhere on the page to keep your session active.';

        // Show the alert to the user
        alert(warningMessage);

        // Reset session activity on any user interaction to restart the timer
        document.addEventListener('click', resetSessionTimeout);
        document.addEventListener('keypress', resetSessionTimeout);
    }, warningTimeMs);
}

/**
 * Reset session timeout
 *
 * This function is called when the user interacts with the page after the timeout warning.
 * It would typically make a request to the server to reset the session timer.
 *
 * For now, it simply logs the action. In a production environment, you would make
 * an AJAX request to the server to reset the session.
 */
function resetSessionTimeout() {
    // Remove the event listeners to avoid resetting multiple times
    document.removeEventListener('click', resetSessionTimeout);
    document.removeEventListener('keypress', resetSessionTimeout);

    // In a real application, you would make an AJAX request here to reset the session:
    // fetch('/api/session/reset', { method: 'POST' })
    //     .then(response => response.json())
    //     .catch(error => console.error('Failed to reset session:', error));

    console.log('Session timeout reset by user activity');
}
