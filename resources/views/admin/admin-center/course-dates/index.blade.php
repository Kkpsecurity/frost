@extends('adminlte::page')

@section('title', $content['title'])

@section('content_header')
    <x-admin.widgets.admin-header />
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            @include('admin.admin-center.course-dates.partials.header')

            @include('admin.admin-center.course-dates.partials.stats')

            @include('admin.admin-center.course-dates.partials.filters')

            @include('admin.admin-center.course-dates.partials.table')
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this course date? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .small-box .inner h3 {
            font-size: 2.2rem;
        }
        .table td {
            vertical-align: middle;
        }
        .btn-group-sm > .btn {
            margin-right: 2px;
        }
        .badge-sm {
            font-size: 0.7em;
        }
        /* Custom grid for 5-column layout */
        @media (min-width: 992px) {
            .col-lg-2-4 {
                flex: 0 0 20%;
                max-width: 20%;
            }
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Toggle active status
            $('.toggle-active-btn').on('click', function() {
                const btn = $(this);
                const courseId = btn.data('id');
                const isActive = btn.data('active');

                $.ajax({
                    url: `/admin/course-dates/${courseId}/toggle-active`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload(); // Refresh to update status
                            toastr.success(response.message);
                        }
                    },
                    error: function() {
                        toastr.error('Error updating status');
                    }
                });
            });

            // Delete confirmation
            $('.delete-btn').on('click', function() {
                const courseId = $(this).data('id');
                $('#deleteForm').attr('action', `/admin/course-dates/${courseId}`);
                $('#deleteModal').modal('show');
            });
        });
    </script>
@stop
