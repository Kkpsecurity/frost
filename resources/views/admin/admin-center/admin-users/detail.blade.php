@extends('layouts.admin')
<?php
    define('TITLE', $content['title']);
?>
@section('page-title')
    {{ __(TITLE) }}
@endsection

@section('breadcrumbs')
    <i class="fa fa-users"></i> {{ __(TITLE) }}:
@endsection
<?php
    $user = $content['user'];
    $tabs['profile'] = [
        'icon'          => 'fa-user',
    ];
    $tabs['login'] = [
        'icon'          => 'fa-lock',
    ];
    $tabs['avatar'] = [
        'icon'          => 'fa-upload',
    ];
    $tabs['orders'] = [
        'icon'          => 'fa-shopping-bag',
    ];

    $active_tab         = Request()->segment(6) ?? 'profile';
?>
@section('content')
    <style>
        .lms-tabs {
            border-radius: 0;
            background: #ccc;
        }
        .lms-tabs:hover, .lms-tabs.active {
            background: #fff;
            border-bottom: 0;
            color: #111;
        }
        .card {
            border-top: 0;
        }
    </style>
    <div class="col-lg-12">
        @include('admin.partials.admin-messages')
    </div>
    <div class="col-lg-12 p-3">
        <h4>{{ __('Manage User Account') }}: {{ $user->fname }} {{ $user->lname }}</h4>
    </div>

    @foreach($tabs as $tab_id => $tab)
        <a href="{{ route('admin.admin-center.adminusers.edit', [$user->id, $tab_id]) }}" class="lms-tabs btn btn-default {{ ($tab_id == $active_tab ? 'active' : '') }}">
            <i class="fa {{ $tab['icon'] }}"></i> @lang(ucwords(humanize($tab_id)))
        </a>
    @endforeach

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <?php $tab_id = $active_tab; ?>
                        @include('admin.admin-center.admin-users.partials.' . $tab_id)
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        // =========================================================================
        // PASSWORD STRENGTH
        // =========================================================================
        function passwordStrength(password) {
            var desc = [
                {'width':'0px'},
                {'width':'20%'},
                {'width':'40%'},
                {'width':'60%'},
                {'width':'80%'},
                {'width':'100%'}
            ];
            var descClass = [
                '',
                'progress-bar-danger',
                'progress-bar-danger',
                'progress-bar-warning',
                'progress-bar-success',
                'progress-bar-success'
            ];
            var score = 0;
            // if password bigger than 8 give 1 point
            if (password.length > 8) score++;
            // if password has both lower and uppercase characters give 1 point
            if ((password.match(/[a-z]/)) && (password.match(/[A-Z]/))) score++;
            // if password has at least one number give 1 point
            if ( password.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/) ) score++;
            // if password has at least one special caracther give 1 point
            if ( password.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/) )	score++;
            // if password bigger than 12 give another 1 point
            if (password.length > 10) score++;
            // display indicator
            $("#jak_pstrength").removeClass(descClass[score-1])
                .addClass(descClass[score])
                .css(desc[score]);
        }
        $(document).ready(function() {
            $("input#old-password").empty().focus();
            $("input#password").keyup(function() {
                passwordStrength($(this).val());
            });
        })
    </script>
@endsection
