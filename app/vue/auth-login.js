/**
* Toast Configuration and Vue App Setup for Login Form
*
* This script configures Toast (a Swal component) and creates a Vue App instance
* to manage the login form.
*
* @category Frontend
* @package Toast and Vue App Configuration
* @author José Caruajulca
*/

// Configure Toast for notifications
const Toast = Swal.mixin({
    toast: true, // Toast notifications
    position: "top-end", // Position at the top right
    showConfirmButton: false, // Do not show confirmation button
    timer: 3000, // Default duration of 3 seconds
    timerProgressBar: true, // Progress bar during the duration
    didOpen: (toast) => {
        // Pause timer when hovering over the notification
        toast.onmouseenter = Swal.stopTimer;
        // Resume timer when mouse leaves the notification
        toast.onmouseleave = Swal.resumeTimer;
    }
});

// Create Vue App instance
const app = Vue.createApp({
    data() {
        return {
            csrfToken: document.getElementById('app').dataset.csrfToken || '',
            formData: {
                usernameOrEmail: '',
                password: ''
            }
        };
    },
    methods: {
        /**
         * Handle login form submission
         *
         * This method sends a POST request to the backend with form data
         * and handles responses, showing Toast notifications or redirecting as needed.
         */
        formLogin() {
            const formData = new FormData();
            formData.append('csrf_token', this.csrfToken);
            formData.append('username_or_email', this.formData.usernameOrEmail);
            formData.append('password', this.formData.password);

            fetch('backend', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success notification
                        Swal.fire({
                            icon: 'success',
                            title: data.success_message,
                            showConfirmButton: true
                        }).then((result) => {
                            // Redirect if confirmed and there is a redirect URL
                            if (result.isConfirmed && data.redirect) {
                                window.location.href = data.redirect;
                            }
                        });
                    } else {
                        // Show error notification
                        Toast.fire({
                            icon: "error",
                            title: data.error_message
                        });
                    }
                })
                .catch(error => {
                    // Show error notification in case of request failure
                    Toast.fire({
                        icon: "error",
                        title: error
                    });
                });
        }
    }
});

// Mount the Vue App on the element with the id 'app' in the DOM
app.mount('#app');
