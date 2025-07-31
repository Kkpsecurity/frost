@extends('adminlte::page')

@section('title', 'AdminLTE Settings')

@section('content_header')
    <x-admin.admin-header />
@stop

@section('content')
    <div class="container-fluid">
        <!-- Success/Error Messages -->
        <x-admin.widgets.messages />

        <div class="row">
            <div class="col-md-9">
                <div class="card card-primary card-outline card-tabs mt-3">
                    <div class="card-header p-0 pt-1 border-bottom-0">
                        <x-admin.widgets.admin-adminlte-config-tabs :activeTab="session('active_tab', 'title-logo')" />
                    </div>

                    <x-admin.forms.admin-adminlte-config-content :activeTab="session('active_tab', 'title-logo')" :adminlteSettings="$adminlteSettings" />
                </div>



                @sysadmin
                    <x-admin.debug.adminlte-debug-card :adminlteSettings="$adminlteSettings" />
                @else
                    <div class="alert alert-warning">
                        <strong>Debug:</strong> {{ '@' }}sysadmin directive condition failed. You need to be an System
                        Administrator to see debug information.
                        {{-- Debug: User Role Info --}}
                        @if (Auth::check())
                            <div class="alert alert-info mb-3" style="font-size: 0.9em;">
                                <strong>Debug Info:</strong>
                                User ID: {{ Auth::user()->id }} |
                                Role ID: {{ Auth::user()->role_id }} ({{ gettype(Auth::user()->role_id) }}) |
                                IsSysAdmin(): {{ Auth::user()->IsSysAdmin() ? 'true' : 'false' }} |
                                Auth::check(): {{ Auth::check() ? 'true' : 'false' }}
                            </div>
                        @endif
                    </div>
                @endsysadmin
            </div>

            <div class="col-md-3">
                <x-admin.widgets.admin-adminlte-settings-sidebar :activeTab="session('active_tab', 'title-logo')" :adminlteSettings="$adminlteSettings" />
            </div>
        </div>
    </div>
@stop

@section('css')
    @vite('resources/css/admin.css')
    @vite('resources/css/adminlte-config-tabs.css')
@stop

@section('js')
    <script>
        function previewTheme() {
            alert('Theme preview functionality coming soon!');
        }

        function clearCache() {
            if (confirm('Are you sure you want to clear the application cache?')) {
                alert('Cache clearing functionality coming soon!');
            }
        }

        // Global function for sidebar preview
        function applySidebarPreview() {
            var $body = $('body');
            var $sidebar = $('.main-sidebar');

            // Get current sidebar settings
            var sidebarCollapsed = $('input[name="sidebar_collapse"]').is(':checked');
            var sidebarMini = $('select[name="sidebar_mini"]').val();
            var sidebarDisable = $('input[name="sidebar_disable_expand"]').is(':checked');
            var sidebarFixed = $('input[name="layout_fixed_sidebar"]').is(':checked');

            // Apply/remove sidebar-collapse class
            if (sidebarCollapsed) {
                $body.addClass('sidebar-collapse');
                console.log('✅ Sidebar collapsed applied');
            } else {
                $body.removeClass('sidebar-collapse');
                console.log('✅ Sidebar expanded applied');
            }

            // Apply/remove sidebar-mini class
            if (sidebarMini) {
                $body.addClass('sidebar-mini');
                console.log('✅ Sidebar mini applied');
            } else {
                $body.removeClass('sidebar-mini');
                console.log('✅ Sidebar mini removed');
            }

            // Apply/remove sidebar-no-expand class
            if (sidebarDisable) {
                $body.addClass('sidebar-no-expand');
                console.log('✅ Sidebar no-expand applied');
            } else {
                $body.removeClass('sidebar-no-expand');
                console.log('✅ Sidebar no-expand removed');
            }

            // Show visual feedback
            var status = sidebarCollapsed ? 'Collapsed' : 'Expanded';
            if (sidebarMini) status += ' (Mini)';
            if (sidebarDisable) status += ' (No Expand)';

            // Create temporary notification
            var $notification = $('<div class="alert alert-success alert-dismissible fade show sidebar-notification">' +
                '<i class="fas fa-check-circle"></i> <strong>Sidebar Updated:</strong> ' + status +
                '<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>' +
                '</div>');

            $('body').append($notification);

            // Auto-remove notification after 4 seconds
            setTimeout(function() {
                $notification.fadeOut(function() {
                    $(this).remove();
                });
            }, 4000);
        }

        function debugForm() {
            console.log('=== FORM DEBUG INFO ===');
            console.log('Form action:', $('form').attr('action'));
            console.log('Form method:', $('form').attr('method'));

            // Check for invalid form controls
            var invalidControls = $('form')[0].querySelectorAll(':invalid');
            console.log('Invalid form controls:', invalidControls.length);
            invalidControls.forEach(function(control) {
                console.log('Invalid control:', control.name, 'Value:', control.value, 'Validation message:',
                    control.validationMessage);
            });

            var formData = new FormData($('form')[0]);
            console.log('Form data entries:');
            var count = 0;
            for (var pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
                count++;
            }
            console.log('Total form fields:', count);

            var $inputs = $('#adminlte-tabContent input, #adminlte-tabContent select');
            console.log('Total input fields found:', $inputs.length);

            alert('Check browser console (F12) for debug information');
        }

        function debugSettings() {
            $('#debug-card').show();

            // Also make an AJAX call to get fresh database data
            console.log('=== DATABASE DEBUG INFO ===');

            // Log current form values vs database values for sidebar settings
            console.log('Current form sidebar settings:');
            $('input[name^="sidebar_"]').each(function() {
                var $input = $(this);
                var name = $input.attr('name');
                var value = $input.is(':checkbox') ? $input.is(':checked') : $input.val();
                console.log(name + ':', value);
            });

            // Check if database is being updated by making a test request
            fetch('/admin/admin-center/settings/adminlte/debug', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log('=== FRESH DATABASE DATA ===');
                    console.log('Database count:', data.database_count);
                    console.log('Config count:', data.config_count);
                    console.log('Raw settings count:', data.raw_count);
                    console.log('Sidebar settings from DB:', data.sidebar_settings);
                    console.log('Last updated:', data.last_updated);
                    console.log('Raw database settings (first 10):', data.raw_settings);
                    console.log('Database settings (first 10):', data.database_settings);
                    console.log('Config settings (first 10):', data.config_settings);

                    // Update the debug card with fresh data
                    var sidebarHtml = '<h6><strong>Live Sidebar Settings from Database:</strong></h6><ul>';
                    Object.keys(data.sidebar_settings).forEach(function(key) {
                        sidebarHtml += '<li><code>' + key + '</code>: <span class="badge badge-success">' + data
                            .sidebar_settings[key] + '</span></li>';
                    });
                    sidebarHtml += '</ul>';

                    $('#database-debug').parent().parent().append('<div class="col-12 mt-3">' + sidebarHtml + '</div>');

                    alert('Database debug completed! Check browser console (F12) for detailed comparison. Fresh data loaded at: ' +
                        data.last_updated);
                })
                .catch(error => {
                    console.error('Error fetching fresh data:', error);
                    alert('Error fetching fresh database data. Check console for details.');
                });
        }

        // Auto-save draft functionality
        $(document).ready(function() {
            // Restore active tab from session or localStorage
            var activeTab = null;

            @if (session('active_tab'))
                activeTab = '{{ session('active_tab') }}';
            @else
                activeTab = localStorage.getItem('adminlte_active_tab');
            @endif

            if (activeTab) {
                $('.nav-tabs a[href="' + activeTab + '"]').tab('show');
                console.log('✅ Restored tab:', activeTab);
            }

            // Save active tab when changed
            $('.nav-tabs a').on('shown.bs.tab', function(e) {
                localStorage.setItem('adminlte_active_tab', $(e.target).attr('href'));
            });

            // Auto-save form data to localStorage every 30 seconds
            setInterval(function() {
                var formData = {};
                $('#adminlte-tabContent input, #adminlte-tabContent select').each(function() {
                    formData[$(this).attr('name')] = $(this).val();
                });
                localStorage.setItem('adminlte_draft', JSON.stringify(formData));
            }, 30000);

            // Bind real-time preview to sidebar settings
            $(document).on('change', 'input[name^="sidebar_"]', function() {
                console.log('Sidebar setting changed:', $(this).attr('name'), 'Value:', $(this).val());
                setTimeout(applySidebarPreview, 100); // Small delay to ensure DOM is updated
            });

            // Enhanced form submission with debugging
            $('form').on('submit', function(e) {
                console.log('=== FORM SUBMISSION DEBUG ===');
                console.log('Form submission triggered');

                // Save current active tab before submission
                var currentTab = $('.nav-tabs .nav-link.active').attr('href');
                localStorage.setItem('adminlte_active_tab', currentTab);

                // Add current tab as hidden field to maintain state after redirect
                var existingTabField = $('input[name="current_tab"]');
                if (existingTabField.length) {
                    existingTabField.val(currentTab);
                } else {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'current_tab',
                        value: currentTab
                    }).appendTo(this);
                }

                // Enhanced debugging - show exactly what's being submitted
                var formData = new FormData(this);
                console.log('=== FORM DATA BEING SUBMITTED ===');
                var sidebarData = {};
                var allData = {};
                var dataCount = 0;

                for (var pair of formData.entries()) {
                    allData[pair[0]] = pair[1];
                    if (pair[0].startsWith('sidebar_')) {
                        sidebarData[pair[0]] = pair[1];
                    }
                    dataCount++;
                }

                console.log('Total fields being submitted:', dataCount);
                console.log('Sidebar settings being submitted:', sidebarData);
                console.log('All form data:', allData);

                if (dataCount === 0) {
                    e.preventDefault();
                    alert(
                        'No settings data found to update. Please make sure the settings are loaded properly.');
                    return false;
                }

                // Show loading state
                var $submitBtn = $('button[type="submit"]');
                var originalText = $submitBtn.html();
                $submitBtn.prop('disabled', true).html(
                '<i class="fas fa-spinner fa-spin"></i> Updating...');

                localStorage.removeItem('adminlte_draft');
                console.log('AdminLTE settings are being updated...');
                console.log('Current tab will be restored:', currentTab);

                // Re-enable button after 3 seconds in case of issues
                setTimeout(function() {
                    $submitBtn.prop('disabled', false).html(originalText);
                }, 3000);
            });

            // Debug button click
            $('button[type="submit"]').on('click', function(e) {
                console.log('Submit button clicked');
                console.log('Form action:', $('form').attr('action'));
                console.log('Form method:', $('form').attr('method'));
            });

            // Auto-apply sidebar collapse changes immediately
            $(document).on('change', 'input[name="sidebar_collapse"]', function() {
                var isCollapsed = $(this).is(':checked');
                var $body = $('body');

                if (isCollapsed) {
                    $body.addClass('sidebar-collapse');
                    console.log('✅ Sidebar auto-collapsed');
                } else {
                    $body.removeClass('sidebar-collapse');
                    console.log('✅ Sidebar auto-expanded');
                }

                // Show brief notification
                var status = isCollapsed ? 'Collapsed' : 'Expanded';
                var $notification = $('<div class="alert alert-info alert-dismissible fade show position-fixed" style="top: 70px; right: 20px; z-index: 9999; width: 300px;">' +
                    '<i class="fas fa-info-circle"></i> <strong>Sidebar ' + status + '</strong> (Preview)' +
                    '<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>' +
                    '</div>');

                $('body').append($notification);

                // Auto-remove notification after 2 seconds
                setTimeout(function() {
                    $notification.alert('close');
                }, 2000);
            });

            // Auto-apply sidebar mini changes immediately
            $(document).on('change', 'select[name="sidebar_mini"]', function() {
                var miniValue = $(this).val();
                var $body = $('body');

                // Remove all sidebar-mini classes
                $body.removeClass('sidebar-mini sidebar-mini-md sidebar-mini-xs');

                if (miniValue && miniValue !== '') {
                    if (miniValue === 'lg') {
                        $body.addClass('sidebar-mini');
                    } else if (miniValue === 'md') {
                        $body.addClass('sidebar-mini sidebar-mini-md');
                    } else if (miniValue === 'sm') {
                        $body.addClass('sidebar-mini sidebar-mini-xs');
                    }
                    console.log('✅ Sidebar mini mode:', miniValue);
                } else {
                    console.log('✅ Sidebar mini mode disabled');
                }

                // Show brief notification
                var status = miniValue ? 'Mini (' + miniValue.toUpperCase() + ')' : 'Normal Size';
                var $notification = $('<div class="alert alert-info alert-dismissible fade show position-fixed" style="top: 70px; right: 20px; z-index: 9999; width: 300px;">' +
                    '<i class="fas fa-info-circle"></i> <strong>Sidebar ' + status + '</strong> (Preview)' +
                    '<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>' +
                    '</div>');

                $('body').append($notification);

                setTimeout(function() {
                    $notification.alert('close');
                }, 2000);
            });
        });
    </script>
@stop
