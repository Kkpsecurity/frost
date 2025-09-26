@php $auths = $content['CompletedCourseAuths']; @endphp
<div class="table-responsive shadow">
    <table class="table table-striped table-bordered table-hover">

         <h3 class="text-dark">Completed Courses</h3>

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
                            <span class="badge bg-success">
                                Completed
                            </span>
                        </td>

                        <td class="text-end">

                            @if ($auth->range_date_id)
                                <a class="btn btn-info" href="{{ route('range_date.show', $auth) }}">
                                    <i class="fa fa-calendar"></i> Range
                                </a>
                                <a class="btn btn-primary" href="{{ route('certificate.g20h_pdf', $auth) }}">
                                    <i class="fas fa-file-pdf"></i> PDF Cert
                                </a>
                            @endif

                            <a class="btn btn-success" href="{{ route('classroom.exam', $auth->LatestExamAuth) }}">
                                <i class="fa fa-eye"></i> View Results
                            </a>

                        </td>

                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
    {{ $auths->render() }}
</div>
