@extends('adminlte::page')

@section('title', 'Search Results')

@section('content_header')
    <x-admin.partials.titlebar title="Search Results" :breadcrumbs="[['title' => 'Admin', 'url' => url('admin')], ['title' => 'Search']]" />
@endsection

@section('content')
    <div class="container-fluid">

        {{-- Search summary --}}
        <div class="row mb-3">
            <div class="col-12">
                <p class="text-muted mb-0">
                    Showing results for: <strong>{{ $query }}</strong>
                </p>
            </div>
        </div>

        {{-- Students / Users --}}
        @if ($users->isNotEmpty())
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-users mr-1"></i>
                                Students
                                <span class="badge badge-primary ml-1">{{ $users->count() }}</span>
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr>
                                            <td>{{ $user->id }}</td>
                                            <td>{{ $user->fname }} {{ $user->lname }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td class="text-right">
                                                <a href="{{ url('admin/students/' . $user->id) }}"
                                                    class="btn btn-xs btn-default">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Courses --}}
        @if ($courses->isNotEmpty())
            <div class="row">
                <div class="col-12">
                    <div class="card card-success card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-graduation-cap mr-1"></i>
                                Courses
                                <span class="badge badge-success ml-1">{{ $courses->count() }}</span>
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Price</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($courses as $course)
                                        <tr>
                                            <td>{{ $course->id }}</td>
                                            <td>{{ $course->title }}</td>
                                            <td>${{ number_format($course->price, 2) }}</td>
                                            <td class="text-right">
                                                <a href="{{ url('admin/courses/' . $course->id) }}"
                                                    class="btn btn-xs btn-default">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Discount Codes --}}
        @if ($discounts->isNotEmpty())
            <div class="row">
                <div class="col-12">
                    <div class="card card-warning card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-tag mr-1"></i>
                                Discount Codes
                                <span class="badge badge-warning ml-1">{{ $discounts->count() }}</span>
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Client</th>
                                        <th>Discount</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($discounts as $code)
                                        <tr>
                                            <td><code>{{ $code->code }}</code></td>
                                            <td>{{ $code->client ?? '—' }}</td>
                                            <td>
                                                @if ($code->percent)
                                                    {{ $code->percent }}%
                                                @elseif($code->set_price)
                                                    ${{ number_format($code->set_price, 2) }} flat
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                <a href="{{ url('admin/discount-codes/' . $code->id) }}"
                                                    class="btn btn-xs btn-default">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Course Enrollments (CourseAuth) --}}
        @if ($courseAuths->isNotEmpty())
            <div class="row">
                <div class="col-12">
                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-id-card mr-1"></i>
                                Enrollments
                                <span class="badge badge-info ml-1">{{ $courseAuths->count() }}</span>
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Enrollment ID</th>
                                        <th>Student ID</th>
                                        <th>DOL Tracking</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($courseAuths as $ca)
                                        <tr>
                                            <td>{{ $ca->id }}</td>
                                            <td>
                                                <a
                                                    href="{{ url('admin/students/' . $ca->user_id) }}">{{ $ca->user_id }}</a>
                                            </td>
                                            <td>{{ $ca->dol_tracking ?? '—' }}</td>
                                            <td>
                                                @if ($ca->completed_at)
                                                    <span class="badge badge-success">Completed</span>
                                                @elseif($ca->disabled_at)
                                                    <span class="badge badge-danger">Disabled</span>
                                                @else
                                                    <span class="badge badge-primary">Active</span>
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                <a href="{{ url('admin/students/' . $ca->user_id) }}"
                                                    class="btn btn-xs btn-default">
                                                    <i class="fas fa-user"></i> Student
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- No results --}}
        @if ($users->isEmpty() && $courses->isEmpty() && $discounts->isEmpty() && $courseAuths->isEmpty())
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No results found for "{{ $query }}"</h5>
                            <p class="text-muted">Try a different name, email, course title, or discount code.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
@endsection
