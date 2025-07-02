@extends('layouts.app')

@section('content')
    @include('frontend.partials.breadcrumbs')

    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-lg-6">
                <div class="text-center">
                    <i class="fas fa-check-circle fa-5x text-success mb-4"></i>
                    <h3 class="mb-4">Payment Successful!</h3>

                    @if ($Order->total_price)
                        <h5 class="mb-3">
                            Course: {{ $Order->GetCourse()->title_long }}<br>
                            Payment: ${{ $Order->total_price }}<br>
                            Completed At: {{ $Order->CompletedAt() }}
                        </h5>
                    @endif

                    <h4 class="mb-3">You are now registered for {{ $Order->GetCourse()->title_long }}</h4>
                    <div class="container mt-5">
                        <h3>Course Guidelines</h3>
                        <ul class="list-group">
                            <li class="list-group-item">Online D Course is Monday – Friday from 8AM to 5PM each day (one hour for lunch). You should log in by 7:30AM each morning.</li>
                            <li class="list-group-item">If you log in late for a class, you must wait until that class is complete and the next class begins.</li>
                            <li class="list-group-item">Students can access the website on one device only.</li>
                            <li class="list-group-item">Student will be verified by a photo of themselves and their ID daily.</li>
                            <li class="list-group-item">Students can see/hear the instructor; the instructor cannot see/hear the student. There is a chat box for you to ask/answer questions to/from the instructor; students cannot see other students’ messages in the chatroom.</li>
                            <li class="list-group-item">Students will receive random challenges throughout the course to ensure they are present, if you miss two consecutive challenges you will not receive credit for that lesson.</li>
                            <li class="list-group-item">If you miss a lesson, you have up to 6 months to complete it.</li>
                            <li class="list-group-item">If you miss the classes from 1PM-5PM on a Tuesday, you must make those classes up any Tuesday from 1PM-5PM.</li>
                            <li class="list-group-item">The exam is 170 questions, you have four hours to complete it. Once you begin the exam, you must finish it. If for some reason you don’t finish the exam you have “failed” and you have one more attempt. If you fail the second attempt, you must retake the entire course. You can take the exam at your convivence.</li>
                            <li class="list-group-item">Upon completion of the class and passing the exam, each student will be emailed a certificate of completion. The certificate should match your name as it appears on your government issued ID.</li>
                            <li class="list-group-item">If you have any questions please check the FAQ on the website.</li>
                        </ul>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
@endsection
