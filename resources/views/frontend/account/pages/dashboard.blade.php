@php $user = Auth()->user(); @endphp

<div class="row dashboard-view">
    <div class="alert">
        <h2 class="mb-4">{{ __('Welcome back, ') . $user->first_name }}</h2>
        <p>{{ __('Manage your Profile data and keep your Account up to date.') }}</p>

        <a href="back" class="back-to-menu"><i class="fa fa-times"></i></a>
    </div>

    @if ($user->email_verified_at === null)
        <div class="alert alert-danger" role="alert">
            <i class="fas fa-exclamation-triangle"></i> {{ __('Your have not verified your email') }},
            <a href="{{ url('account/resend/verification') }}"
                class="btn btn-link text-decoration-none">{{ __('Click here to resend.') }}</a>
        </div>
    @endif

    <div class="account-container">
        <div class="account-title">
            <h2>{{ __('About Me') }}</h2>
        </div>
        <ul class="list-group list-group-flush">
            @php
                $user = Auth()->user(); // Get the current authenticated user
                $student_info_json = $user->student_info; // Get the student info JSON
                $student_info_array = $student_info_json;

                // Create an array with the basic user info
                $account = [
                    'name' => $user->fullname(),
                    'email' => $user->email,
                ];

                // If student_info_array is an array and not empty, merge it with the account array
                if (is_array($student_info_array) && !empty($student_info_array)) {
                    $account = array_merge($account, $student_info_array);
                }
            @endphp

            <!-- Rest of your existing HTML code... -->

            @foreach ($account as $key => $value)
                <li class="list-group-item d-flex align-items-center">
                    <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}</strong>
                    @if ($key == 'email')
                        <div class="verification_content ms-auto">
                            <strong>
                                {!! \Collective\Html\HtmlFacade::mailto(Auth()->user()->email) !!}
                                @if ($user->email_verified_at !== null)
                                    <i class="fas fa-check-circle ml-2" data-bs-toggle="tooltip" title="Verified"></i>
                                @else
                                    <i class="fas fa-exclamation-triangle ml-2" data-bs-toggle="tooltip"
                                        title="Unverified"></i>
                                @endif
                            </strong>
                        </div>
                    @else
                        <span class=" ms-auto"><strong>{{ $value }}</strong></span>
                    @endif
                </li>
            @endforeach


        </ul>
    </div>
</div>
