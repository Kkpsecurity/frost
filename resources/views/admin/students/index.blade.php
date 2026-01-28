@extends('adminlte::page')

@section('title', 'Students Management')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Students Management</h1>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Students</h3>
            <div class="card-tools">
                <form action="{{ route('admin.students.index') }}" method="GET" class="form-inline">
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" placeholder="Search students..." value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Courses Enrolled</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td>{{ $student->id }}</td>
                            <td>
                                <strong>{{ $student->fname }} {{ $student->lname }}</strong>
                            </td>
                            <td>{{ $student->email }}</td>
                            <td>
                                <span class="badge badge-primary">
                                    {{ $student->courseAuths->count() }} courses
                                </span>
                            </td>
                            <td>
                                @if($student->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $student->created_at->format('M d, Y') }}</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.students.show', $student->id) }}"
                                       class="btn btn-info"
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.students.edit', $student->id) }}"
                                       class="btn btn-warning"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                <p class="my-3">No students found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($students->hasPages())
            <div class="card-footer clearfix">
                {{ $students->links() }}
            </div>
        @endif
    </div>
@stop

@section('css')
    <style>
        .table td {
            vertical-align: middle;
        }
        .table thead th {
            background-color: #222d32;
            color: #ffc107;
            border-color: #1a2226;
        }
    </style>
@stop

@section('js')
    <script>
        // Auto-submit search form on clear
        $('input[name="search"]').on('search', function() {
            if (this.value === '') {
                $(this).closest('form').submit();
            }
        });
    </script>
@stop
