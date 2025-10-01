@extends('adminlte::page')

@section('title', 'AdminLTE Settings')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>AdminLTE Configuration</h1>
        <div>
            <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Settings
            </a>
        </div>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">AdminLTE Theme Configuration</h3>
            <div class="card-tools">
                <a href="{{ route('admin.settings.debug-adminlte') }}" class="btn btn-sm btn-info">
                    <i class="fas fa-bug"></i> Debug Settings
                </a>
            </div>
        </div>
        <form action="{{ route('admin.settings.update-adminlte') }}" method="POST" id="adminlte-form">
            @csrf
            @method('PUT')
            <input type="hidden" name="current_tab" id="current_tab" value="">

            <div class="card-body">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" id="adminlte-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="layout-tab" data-toggle="tab" href="#layout" role="tab">
                            <i class="fas fa-layout-alt"></i> Layout
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="sidebar-tab" data-toggle="tab" href="#sidebar" role="tab">
                            <i class="fas fa-bars"></i> Sidebar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="navbar-tab" data-toggle="tab" href="#navbar" role="tab">
                            <i class="fas fa-window-maximize"></i> Navbar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="footer-tab" data-toggle="tab" href="#footer" role="tab">
                            <i class="fas fa-window-minimize"></i> Footer
                        </a>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content" id="adminlte-tabContent">
                    <!-- Layout Tab -->
                    <div class="tab-pane fade show active" id="layout" role="tabpanel">
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="layout_dark_mode">Dark Mode</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="layout_dark_mode" name="layout_dark_mode" value="1" {{ ($adminlteSettings['layout_dark_mode'] ?? false) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="layout_dark_mode">Enable Dark Mode</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="layout_fixed_sidebar">Fixed Sidebar</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="layout_fixed_sidebar" name="layout_fixed_sidebar" value="1" {{ ($adminlteSettings['layout_fixed_sidebar'] ?? false) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="layout_fixed_sidebar">Fix Sidebar Position</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="layout_fixed_navbar">Fixed Navbar</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="layout_fixed_navbar" name="layout_fixed_navbar" value="1" {{ ($adminlteSettings['layout_fixed_navbar'] ?? false) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="layout_fixed_navbar">Fix Navbar Position</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="layout_fixed_footer">Fixed Footer</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="layout_fixed_footer" name="layout_fixed_footer" value="1" {{ ($adminlteSettings['layout_fixed_footer'] ?? false) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="layout_fixed_footer">Fix Footer Position</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="layout_boxed">Boxed Layout</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="layout_boxed" name="layout_boxed" value="1" {{ ($adminlteSettings['layout_boxed'] ?? false) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="layout_boxed">Enable Boxed Layout</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="layout_top_nav">Top Navigation</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="layout_top_nav" name="layout_top_nav" value="1" {{ ($adminlteSettings['layout_top_nav'] ?? false) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="layout_top_nav">Use Top Navigation Instead of Sidebar</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Tab -->
                    <div class="tab-pane fade" id="sidebar" role="tabpanel">
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sidebar_mini">Sidebar Mini</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="sidebar_mini" name="sidebar_mini" value="1" {{ ($adminlteSettings['sidebar_mini'] ?? false) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="sidebar_mini">Enable Mini Sidebar</label>
                                    </div>
                                    <small class="form-text text-muted">Sidebar will collapse to icons only</small>
                                </div>

                                <div class="form-group">
                                    <label for="sidebar_collapse">Sidebar Collapsed</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="sidebar_collapse" name="sidebar_collapse" value="1" {{ ($adminlteSettings['sidebar_collapse'] ?? false) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="sidebar_collapse">Start with Collapsed Sidebar</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="sidebar_flat_style">Flat Style</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="sidebar_flat_style" name="sidebar_flat_style" value="1" {{ ($adminlteSettings['sidebar_flat_style'] ?? false) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="sidebar_flat_style">Use Flat Sidebar Styling</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sidebar_legacy_style">Legacy Style</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="sidebar_legacy_style" name="sidebar_legacy_style" value="1" {{ ($adminlteSettings['sidebar_legacy_style'] ?? false) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="sidebar_legacy_style">Use Legacy Sidebar Styling</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="sidebar_compact">Compact Sidebar</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="sidebar_compact" name="sidebar_compact" value="1" {{ ($adminlteSettings['sidebar_compact'] ?? false) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="sidebar_compact">Use Compact Sidebar</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="sidebar_child_indent">Child Indent</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="sidebar_child_indent" name="sidebar_child_indent" value="1" {{ ($adminlteSettings['sidebar_child_indent'] ?? false) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="sidebar_child_indent">Indent Child Menu Items</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navbar Tab -->
                    <div class="tab-pane fade" id="navbar" role="tabpanel">
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="navbar_elevation">Navbar Elevation</label>
                                    <select class="form-control" id="navbar_elevation" name="navbar_elevation">
                                        <option value="0" {{ ($adminlteSettings['navbar_elevation'] ?? '0') == '0' ? 'selected' : '' }}>No Elevation</option>
                                        <option value="1" {{ ($adminlteSettings['navbar_elevation'] ?? '0') == '1' ? 'selected' : '' }}>Small</option>
                                        <option value="2" {{ ($adminlteSettings['navbar_elevation'] ?? '0') == '2' ? 'selected' : '' }}>Medium</option>
                                        <option value="3" {{ ($adminlteSettings['navbar_elevation'] ?? '0') == '3' ? 'selected' : '' }}>Large</option>
                                        <option value="4" {{ ($adminlteSettings['navbar_elevation'] ?? '0') == '4' ? 'selected' : '' }}>Extra Large</option>
                                    </select>
                                    <small class="form-text text-muted">Controls the navbar shadow</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="navbar_no_border">No Border</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="navbar_no_border" name="navbar_no_border" value="1" {{ ($adminlteSettings['navbar_no_border'] ?? false) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="navbar_no_border">Remove Navbar Border</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Tab -->
                    <div class="tab-pane fade" id="footer" role="tabpanel">
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="footer_fixed">Fixed Footer</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="footer_fixed" name="footer_fixed" value="1" {{ ($adminlteSettings['footer_fixed'] ?? false) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="footer_fixed">Fix Footer at Bottom</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="footer_border">Footer Border</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="footer_border" name="footer_border" value="1" {{ ($adminlteSettings['footer_border'] ?? true) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="footer_border">Show Footer Border</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Settings
                </button>
                <button type="button" class="btn btn-secondary" onclick="resetForm()">
                    <i class="fas fa-undo"></i> Reset
                </button>
                <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Restore active tab from session or hash
            @if(session('active_tab'))
                var activeTab = '{{ session('active_tab') }}';
                $('a[href="' + activeTab + '"]').tab('show');
            @else
                if (window.location.hash) {
                    $('a[href="' + window.location.hash + '"]').tab('show');
                }
            @endif

            // Update hidden field when tab changes
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                var target = $(e.target).attr('href');
                $('#current_tab').val(target);
                history.replaceState(null, null, target);
            });

            // Set initial tab value
            var initialTab = $('.nav-link.active').attr('href');
            $('#current_tab').val(initialTab);
        });

        function resetForm() {
            if (confirm('Are you sure you want to reset all AdminLTE settings? This will restore default values.')) {
                document.getElementById('adminlte-form').reset();
            }
        }
    </script>
@stop
