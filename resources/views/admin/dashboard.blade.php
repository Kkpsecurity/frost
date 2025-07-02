{{-- array:4 [▼ // resources/views/admin/dashboard.blade.php
  "available_widgets" => array:9 [▼
    0 => "."
    1 => ".."
    2 => "1_new-users.blade.php"
    3 => "2_total-users.blade.php"
    4 => "3_unconfirmed-users.blade.php"
    5 => "4_latest-registrations.blade.php"
    6 => "class_d_students.blade.php"
    7 => "class_g_students.blade.php"
    8 => "total_student_in_all_classes.blade.php"
  ]
  "users_stats" => {#1203 ▼
    +"new_students": 101
    +"total_students": 937
    +"unverified": 493
  }
  "student_counts" => array:2 [▼
    0 => {#1154 ▼
      +"title": "Florida D40"
      +"count": 1
    }
    1 => {#1160 ▼
      +"title": "Total"
      +"count": 1
    }
  ]
  "latestRegistrations" => Illuminate\Database\Eloquent\Collection {#1081 ▶}
]
 --}}




@extends('layouts.admin')
@php $widgets = $content['widgets']; @endphp

@section('content')
    <div class="col-md-12 p-5 m-t-20">
       
        <div class="row align-items-center">
            <div class="col-md-4">
                <h2 class="text-primary">{{ config('app.name') }}</h2>
            </div>
            <div class="col-md-5">
                <h3 class="text-secondary">{{ __('Welcome back') }}: <strong>{{ Auth()->User()->fullname() }}</strong></h3>
            </div>
            <div class="col-md-3">
                <div class="d-grid">
                    <h3 class="text-secondary">{{ dateGreeter() }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div id="content" class="container-fluid p-5">
        <div class="row">
            <div class="col-md-9">

                <div class="row"> <!-- Note the change here -->
                    @php $x=0; @endphp
                    @foreach ($widgets['available_widgets'] as $widget)
                        @if ($widget != '.' && $widget != '..' && $widget != '4_latest-registrations.blade.php')
                            <?php $widget = str_replace('.blade.php', '', $widget); ?>
                            <div class="col-md-4">
                                @php
                                    $shouldIncludeWidget = false;
                                    foreach ($widgets['student_counts'] as $studentCount) {                                       
                                        if (in_array($studentCount->title, ['Florida D40', 'Florida G28', 'Total'])) {                                           
                                            $shouldIncludeWidget = true;
                                        }
                                    }
                                @endphp

                                @if ($shouldIncludeWidget)
                                    @include('admin.plugins.widgets.dashboard.' . $widget)
                                @endif
                            </div>
                        @endif
                    @endforeach

                </div>
            </div>
            <div class="col-md-3">
                <div class="row">
                    <div class="col-md-12 text-center p-3">
                        <h3>{{ __('Support Team') }}</h3>
                        <a href="{{ route('admin.frost-support.dashboard') }}" 
                        class="btn btn-primary btn-lg btn-rounded">{{ __('Frost Support') }}</a>
                    </div>
                </div>


                @if (in_array('4_latest-registrations.blade.php', $widgets['available_widgets']))
                    @include('admin.plugins.widgets.dashboard.4_latest-registrations')
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/admin/dist/js/pages/dashboard.js') }}"></script>
  
@endsection
