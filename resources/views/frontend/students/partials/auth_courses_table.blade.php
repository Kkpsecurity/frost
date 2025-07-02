@php $mergedAuths = $content['MergedCourseAuths']; @endphp
<div class="table-responsive shadow">
    <table class="table table-striped table-bordered table-hover">
        <h3 class="text-dark">{{ Auth()->user()->fullname() }}'s Dashboard</h3>
        <thead class="table-head frost-primary-bg">
            <tr>
                <th class="text-white">{{ __('Date Purchased') }}</th>
                <th class="text-white">{{ __('Item') }}</th>
                <th class="text-white">{{ __('Expires') }}</th>
                <th class="text-white text-end">{{ __('Action') }}</th>
            </tr>
        </thead>
        <tbody>
            @if ($mergedAuths->count() < 1)
                <tr>
                    <td colspan="5" class="text-center">
                        <div class="alert alert-danger">No Courses Found</div>
                    </td>
                </tr>
            @else
                @foreach ($mergedAuths as $auth)
                    <tr>
                        <td>{{ $auth->CreatedAt('MMMM D, Y') }}</td>
                        <td>{{ $auth->course->title_long }}</td>
                        <td>
                            {{ $auth->expire_date ? Carbon\Carbon::parse( $auth->expire_date )->isoFormat( 'MMMM D, Y' ) : 'Never' }}
                        </td>
                       
                        @if ($auth->ClassroomButton())
                            <td class="text-end">
                                <a class="btn btn-success" href="{{ route('classroom.portal.class', [$auth->id]) }}">
                                    <i class="fa fa-eye"></i> View Course
                                </a>
                            </td>
                        @elseif($auth->isExpired())
                            <td class="text-end">
                                <div class="btn btn-danger" >
                                    <i class="fa fa-eye"></i> Course Expired
                                </div>
                            </td>
                        @elseif($auth->IsFailed())
                            <td class="text-end">
                                <div class="btn btn-danger" >
                                    <i class="fa fa-eye"></i> Course Failed
                                </div>
                            </td>
                        @else
                            <td class="text-end">
                                <a class="btn btn-dark disabled" href="#">
                                    <i class="fa fa-eye-slash"></i> Course Not Ready
                                </a>
                            </td>
                        @endif
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>



































{{-- @php $auths = $content['CourseAuths']; @endphp
<div class="table-responsive shadow">
    <table class="table table-striped table-bordered table-hover">
        <h3 class="text-dark">{{ Auth()->user()->fullname() }}'s Dashboard</h3>
        <thead class="table-head frost-primary-bg">
            <tr>
                <th class="text-white">{{ __('Date Purchased') }}</th>
                <th class="text-white">{{ __('Item') }}</th>
                <th class="text-white">{{ __('Expires') }}</th>
                <th class="text-white">{{ __('Status') }}</th>
                <th class="text-white text-end">{{ __('Action') }}</th>
            </tr>
        </thead>
        <tbody>
            @if ($auths->count() < 1)
                <tr>
                    <td colspan="5" class="text-center">
                        <div class="alert alert-danger">No Orders Found</div>
                    </td>
                </tr>
            @else
                @foreach ($auths as $auth)
                    <tr>
                        <td>{{ $auth->CreatedAt('MMMM d, Y') }}</td>
                        <td>{{ $auth->course->title_long }}</td>
                        <td>
                            {{ $auth->expires_at ? $auth->ExpiresAt('MMMM d, Y') : 'Never' }}
                        </td>
                        <td>
                            <span class="badge {{ $auth->status == 'Completed' ? 'bg-success' : 'bg-warning' }}">
                                {{ $auth->status ?? 'Pending' }}
                            </span>
                        </td>

                        @if ($auth->ClassroomButton())
                            <td class="text-end">
                                <a class="btn btn-success" href="{{ route('classroom.portal.class', [$auth->id]) }}">
                                    <i class="fa fa-eye"></i> View Course
                                </a>
                            </td>
                        @else
                            <td class="text-end">
                                <a class="btn btn-dark disabled" href="#">
                                    <i class="fa fa-eye-slash"></i> Course Not Ready
                                </a>
                            </td>
                        @endif

                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
    {{ $auths->render() }}
</div> --}}
