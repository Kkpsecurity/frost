<div class="card card-secondary collapsed-card mb-3">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-filter"></i> Filters & Search
        </h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-plus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.course-dates.index') }}">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="course_id">Course</label>
                        <select name="course_id" id="course_id" class="form-control">
                            <option value="">All Courses</option>
                            @foreach($content['courses'] as $course)
                                <option value="{{ $course->id }}"
                                        @if($content['filters']['course_id'] == $course->id) selected @endif>
                                    {{ $course->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="all" @if($content['filters']['status'] == 'all') selected @endif>All</option>
                            <option value="active" @if($content['filters']['status'] == 'active') selected @endif>Active</option>
                            <option value="inactive" @if($content['filters']['status'] == 'inactive') selected @endif>Inactive</option>
                            <option value="upcoming" @if($content['filters']['status'] == 'upcoming') selected @endif>Upcoming</option>
                            <option value="past" @if($content['filters']['status'] == 'past') selected @endif>Past</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="date_range">Date Range</label>
                        <select name="date_range" id="date_range" class="form-control">
                            <option value="month" @if($content['filters']['date_range'] == 'month') selected @endif>This Month</option>
                            <option value="week" @if($content['filters']['date_range'] == 'week') selected @endif>This Week</option>
                            <option value="year" @if($content['filters']['date_range'] == 'year') selected @endif>This Year</option>
                            <option value="all" @if($content['filters']['date_range'] == 'all') selected @endif>All Time</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="instructor_id">Instructor</label>
                        <select name="instructor_id" id="instructor_id" class="form-control">
                            <option value="">All Instructors</option>
                            @foreach($content['instructors'] as $instructor)
                                <option value="{{ $instructor->id }}"
                                        @if($content['filters']['instructor_id'] == $instructor->id) selected @endif>
                                    {{ $instructor->fname }} {{ $instructor->lname }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="{{ route('admin.course-dates.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
