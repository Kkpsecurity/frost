@php $mergedAuths = $content['MergedCourseAuths']; @endphp
<div class="table-responsive list-group-table shadow">
    <div class="list-group">
        @if ($mergedAuths->count() < 1)
            <div class="alert alert-danger">No Courses Found</div>
        @else
            @foreach ($mergedAuths as $auth)
                <div class="list-group-item p-3">
                    <h5 class="mb-1">{{ $auth->course->title_long }}</h5>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small>{{ $auth->CreatedAt('MMMM d, Y') }}</small> |
                            <small>Expires: {{ $auth->expires_at ? $auth->ExpiresAt('MMMM d, Y') : 'Never' }}</small>
                        </div>
                        <div>
                            @if ($auth->ClassroomButton())
                                <td class="text-end">
                                    <a class="btn btn-success" href="{{ route('classroom.portal.class', [$auth->id]) }}">
                                        <i class="fa fa-eye"></i> View Course
                                    </a>
                                </td>
                            @elseif($auth->isExpired())
                                <td class="text-end">
                                    <a class="btn btn-danger" href="{{ route('classroom.portal.class', [$auth->id]) }}">
                                        <i class="fa fa-eye"></i> Course Expired
                                    </a>
                                </td>
                            @else
                                <td class="text-end">
                                    <a class="btn btn-dark disabled" href="#">
                                        <i class="fa fa-eye-slash"></i> Course Not Ready
                                    </a>
                                </td>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

</div>
