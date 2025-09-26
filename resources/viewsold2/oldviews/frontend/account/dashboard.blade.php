@extends('layouts.app')
@php $user = $content['user']; @endphp
@php $segment_view = $content['page']; @endphp
@php $title =  ucwords($segment_view); @endphp

@section('page-title')
    {{ $content['title'] }} | {{ $title }}
@endsection
@section('page-keywords')
    {{ $content['keywords'] }}
@endsection
@section('page-description')
    {{ $content['description'] }}
@endsection

@php
    $data = [
        'menu' => ['dashboard', 'profile', 'password', 'avatar', 'billing'],
        'dashboard_head' => [
            [
                'img_src' => Auth()->user()->getAvatar('thumb'),
                'name' => Auth()->user()->fullname(),
            ],
        ],
        'support_link' => route('pages', 'support'),
        'support_text' => 'Need Help?',
    ];
@endphp

@section('styles')
    <style>
        .my-custom-row {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
        }

        .my-custom-icon {
            color: #007bff;
        }

        .my-custom-title {
            font-size: 30px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .my-custom-lead {
            font-size: 16px;
            margin-top: 10px;
            color: #666;
            text-transform: uppercase;
            font-weight: bold
        }

        @media (max-width: 768px) {
            .dashboard-area {
                padding: 10px;
            }

            .my-custom-row {
                padding: 10px;
            }

            .my-custom-title {
                font-size: 24px;
                /* Smaller font size on mobile */
            }

            .my-custom-lead {
                font-size: 14px;
            }

            .sidebar,
            .dashboard-side,
            .dashboard-content {
                width: 100%;
                /* Full width on small screens */
                margin-bottom: 20px;
            }

            .avatar {
                width: 80px;
                /* Smaller avatars on mobile */
            }

            /* Hide sidebar elements not critical for mobile users */
            .dashboard-side ul,
            .dashboard-support {
                display: none;
            }
        }
    </style>
@endsection


@section('content')
    @include('frontend/partials/breadcrumbs')
    <div class="dashboard-area bg-color area-padding">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-sm-3 col-xs-12 d-none d-md-block">
                    <aside class="sidebar">
                        <div class="dashboard-side">
                            @foreach ($data['dashboard_head'] as $item)
                                <div class="single-dash-head">
                                    <div class="dashboard-profile">
                                        <div class="profile-content">
                                            @php
                                                $imgSrc = isset($item['img_src'])
                                                    ? $item['img_src']
                                                    : ($user->hasDLicense()
                                                        ? $item['img_complete']
                                                        : $item['img_incomplete']);
                                            @endphp
                                            <img src="{{ $imgSrc }}" alt="{{ $item['name'] }}" width="120"
                                                class="avatar">
                                            <span class="pro-name text-white">{{ $item['name'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <ul class="mb-5">
                                @foreach ($data['menu'] as $view)
                                    <li class="{{ $segment_view == $view ? 'active' : '' }}">
                                        <a href="{{ route('account', $view) }}"
                                            class="list-group-item d-flex align-items-center {{ $segment_view == $view ? 'active' : '' }}">
                                            @lang(ucwords($view))
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="dashboard-support">
                            <div class="support-inner">
                                <div class="help-support">
                                    <i class="fa fa-question-mark"></i>
                                    <a href="{{ $data['support_link'] }}"><span
                                            class="help-text">{{ $data['support_text'] }}</span></a>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>

                <div class="col-md-9 col-sm-9 col-xs-12">
                    <div class="dashboard-content" style="height: auto">
                        @include('frontend/partials/messages')
                        @include('frontend/account/pages/' . $segment_view)
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/5.0.7/inputmask.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.dashboard-content').css('min-height', $(window).height() - 200);

            $('#emailCheck').change(function() {
                if ($(this).is(":checked")) {
                    $('#newEmailDiv').show();
                } else {
                    $('#newEmailDiv').hide();
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            var cleavePhone = new Cleave('#phone', {
                phone: true,
                phoneRegionCode: 'US' // Set this to the appropriate country code
            });
        });
    </script>
@endsection
