@props(['activeTab' => 'title-logo', 'adminlteSettings' => []])

<x-admin.forms.settings.adminlte-form :activeTab="$activeTab" :adminlteSettings="$adminlteSettings" />

<script>

    function submitActiveTabOnly(event) {
        event.preventDefault();

        // Get the currently active tab
        const activeTab = document.querySelector('.tab-pane.active');
        if (!activeTab) {
            console.error('No active tab found');
            return;
        }

        const tabId = activeTab.id;
        console.log('Submitting tab:', tabId);

        // Create a new FormData object with only the active tab fields
        const form = event.target.closest('form');
        const formData = new FormData();

        // Add CSRF token and method
        formData.append('_token', document.querySelector('input[name="_token"]').value);
        formData.append('_method', 'PUT');
        formData.append('current_tab', '#' + tabId);

        // Get all form inputs from the active tab only
        const activeTabInputs = activeTab.querySelectorAll('input, select, textarea');

        activeTabInputs.forEach(input => {
            if (input.name && input.name !== '_token' && input.name !== '_method') {
                if (input.type === 'checkbox') {
                    // For checkboxes, only add if checked, or add hidden value if exists
                    if (input.checked) {
                        formData.append(input.name, input.value);
                    } else {
                        // Look for hidden input with same name (for unchecked state)
                        const hiddenInput = activeTab.querySelector(`input[type="hidden"][name="${input.name}"]`);
                        if (hiddenInput) {
                            formData.append(input.name, hiddenInput.value);
                        }
                    }
                } else if (input.type === 'radio') {
                    if (input.checked) {
                        formData.append(input.name, input.value);
                    }
                } else {
                    formData.append(input.name, input.value);
                }
            }
        });

        // Debug: Log what we're about to submit
        console.log('Form data being submitted:');
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }

        // Submit the form data
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (response.redirected) {
                // Follow the redirect
                window.location.href = response.url;
            } else {
                return response.text();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while saving settings.');
        });
    }

    // Track tab changes
    document.addEventListener('DOMContentLoaded', function() {
        const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
        tabButtons.forEach(button => {
            button.addEventListener('shown.bs.tab', function(e) {
                const targetTab = e.target.getAttribute('data-bs-target') || e.target.getAttribute('href');
                document.getElementById('current_tab').value = targetTab;
            });
        });

        // Set initial active tab
        const activeTabPane = document.querySelector('.tab-pane.active');
        if (activeTabPane) {
            document.getElementById('current_tab').value = '#' + activeTabPane.id;
        }
    });
</script>
