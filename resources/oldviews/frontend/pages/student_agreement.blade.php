@extends('layouts.app')

@section('page-title') {{ $content['title'] }} @stop
@section('page-keywords') {{ $content['keywords'] }} @stop
@section('page-description') {{ $content['description'] }} @stop

@php
    $content = [
        'title' => \App\RCache::SiteConfig('site_company_name'),
        'header' => [
            'title' => 'Terms and Conditions',
            'subtitle' => 'Student Contract Online',
            'intro' => 'This contract outlines the terms and conditions for using the online training secure platform to complete the Security Officer Curriculum. You agree to be bound by these terms and conditions by accessing and using the platform.',
        ],
        'terms' => [
            'Enrollment: To enroll in our online courses, you must be 18 years old and have a valid email address. You must also pay the required course fees and agree to these terms and conditions.',
            'Payment: All course fees must be paid in full at the time of enrollment. We accept payment by credit card, debit card, or PayPal.',
            'Refunds for courses a student has enrolled in and commenced will not be granted. Regardless of the completion status of the course, enrolled students are not eligible for refunds.',
            'Course Access: Once enrolled, you will have access to the course materials for six months. If you do not complete the course within six months, you must re-enroll and pay the course fees again.',
            'Intellectual Property: All course materials, including text, graphics, videos, and audio recordings, are the property of our online training school and may not be reproduced or distributed without our written permission.',
            [
                'text' => 'Prohibited Conduct: You are prohibited from engaging in any conduct that may interfere with the proper functioning of our online training school or harm other site users.',
                'sublist' => [
                    'Unauthorized Recording: Students are prohibited from recording, capturing, or otherwise duplicating any part of the online training course without the express written consent of The Security Training Group (STG).',
                    'Sharing Login Credentials: Students are prohibited from sharing their login credentials or allowing others to access the online training course using their login information. Each student is required to register and pay for their own course.',
                    'Disruptive Behavior: Students are prohibited from engaging in disruptive behavior, including but not limited to harassing or threatening other students or instructors, using profanity, or engaging in any activity that interferes with the learning environment of others.',
                    'Cheating: Students are prohibited from cheating on quizzes, exams, or other assessments, including but not limited to using unauthorized materials, communicating with other students during exams, or copying answers from other sources.',
                    'Violating STG Policies: Students are prohibited from violating any of STG\'s policies, including but not limited to the refund policy, code of conduct, and intellectual property policy.',
                    'Violating Laws: Students are prohibited from using the online training course to engage in any activity that violates local, state, or federal laws.',
                    'Unauthorized Access: Students are prohibited from accessing any parts of the online training course for which they do not have authorization or are not intended for their use.',
                    'Misrepresenting Identity: Students are prohibited from misrepresenting their identity, including but not limited to using a false name, email address, or other identifying information.',
                    'Disseminating Confidential Information: Students are prohibited from disseminating confidential information obtained through the online training course to any third party without the express written consent of STG. Prohibited conduct includes, but is not limited to, hacking, phishing, spamming, and distributing malware.',
                ],
            ],
            'Disclaimer of Warranties: Our online training school provides courses on an "as is" and "as available" basis. We make no warranties, express or implied, regarding the accuracy, reliability, or completeness of any course materials or the functionality of our site.',
            'Limitation of Liability: To the extent permitted by law, our online training school will not be liable for any damages, including but not limited to direct, indirect, incidental, special, or consequential damages, arising out of your use of our site or our courses.',
            'Modification of Terms: We reserve the right to modify these terms and conditions at any time. Any modifications will be effective immediately upon posting to our site.',
            'Governing Law: These terms and conditions will be governed by and construed in accordance with the laws of the State of Florida.',
            [
                'text' => 'Duration and Timing: The curriculum consists of a minimum of 40 hours of instruction, with each module timed to ensure compliance with this requirement. The test duration is a minimum of 2 hours, and students must achieve a passing score of 128/170.',
                'sublist' => ['In the event that a student fails the initial test, they will be afforded the opportunity to take one re-test. However, if the student fails the re-test, they will be required to retake the entire course and will be responsible for payment of the course fee in order to retake the course.'],
            ],
            [
                'text' => 'Prohibited Actions: Driving During Course Sessions',
                'sublist' => ['Driving Prohibition: It is strictly prohibited for students to be driving or situated behind the wheel of a motor vehicle during any course session. This policy is in place to ensure the full attention and participation of all students in a safe and controlled environment conducive to learning.', 'Consequences of Violation: If a student is observed to be driving or behind the wheel of a motor vehicle during a course session, they will be immediately ejected from the class day. Furthermore, the student will be required to retake the missed day at their own expense to complete the course requirements. This policy is strictly enforced to maintain the integrity and effectiveness of the training program.'],
            ],
            'Certification: A certificate of completion will be issued to each student who successfully completes the training. The certificate will contain the name and license number of the school, the name(s) and license number(s) of the Class “DI” instructor(s) who conducted the training, and the number of hours of training completed.',
            'Online Training: The online training will be conducted using live instruction from STG with SSL/TLS technology for secure website access. Students must verify their identity using a US state or federal-issued photo identification. Daily attendance will be verified through visual inspection and recorded in a digital log per state requirements.',
            'Student Participation: It is expected that students participate actively throughout the course, with the provision of security questions at random intervals to ensure engagement. Failure to answer security questions correctly may fail non-participation. Should a student\'s screen be locked out, they must contact STG customer support to unlock it. Failure to complete the course hours as mandated by the state will require the student to repeat the missed coursework and complete the required training hours.',
            'Records: STG will maintain records for two years, including a schedule of each class session, course materials and reference sources, the original of each final exam, and a log for each class session. STG will maintain a digital record of the student attendance log for each class session, records of all training sessions, and proof of compliance with all security protocols.',
            'Termination: We reserve the right to terminate access to the platform at any time for any student who violates these terms and conditions or engages in conduct that is disruptive or harmful.',
            'Force Majeure: Neither party shall be liable for any failure to perform its obligations if such failure results from acts of God, civil or military authority, acts of the public enemy, war, riots, civil disturbances, epidemics, or any other cause beyond the reasonable control of that party.',
        ],
        'footer' => [
            'date' => date('F j, Y'),
            'company' => \App\RCache::SiteConfig('site_company_name'),
            'address' => \App\RCache::SiteConfig('site_company_address'),
            'phone' => \App\RCache::SiteConfig('site_support_phone'),
            'email' => \App\RCache::SiteConfig('site_support_email'),
        ],
    ];

@endphp

@section('content')

    @include('frontend.partials.breadcrumbs')

    <div class="terms-area frost-secondary-bg area-padding-2">
        <div class="container pt-3">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="section-headline text-center">
                        <h4 class="text-white">{{ $content['header']['title'] }}</h4>
                        <h5 class="text-white">{{ $content['header']['subtitle'] }}</h5>
                        <p class="text-white">{{ $content['header']['intro'] }}</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="company-terms">
                        <ol>
                            @foreach ($content['terms'] as $index => $term)
                                @if (is_array($term))
                                    <div class="single-terms">
                                        <h4>
                                            <span class="text-white number">{{ $index + 1 }}.</span>
                                            <span
                                                class="text-white condition-text">{{ isset($term['title']) ? $term['title'] : '' }}</span>
                                        </h4>
                                        <p>{{ isset($term['content']) ? $term['content'] : '' }}</p>
                                    </div>
                                    @if (isset($term['sublist']))
                                        <li>
                                            <ol type="a">
                                                @foreach ($term['sublist'] as $subTerm)
                                                    <li class="text-white">{{ $subTerm }}</li>
                                                @endforeach
                                            </ol>
                                        </li>
                                    @endif
                                @else
                                    <div class="single-terms">
                                        <h4>
                                            <span class="text-white number">{{ $index + 1 }}.</span>
                                            <span class="text-white condition-text">{{ $term }}</span>
                                        </h4>
                                    </div>
                                @endif
                            @endforeach
                        </ol>
                    </div>
                </div>
            </div>
            <div>
                <p class="text-white">Date: {{ $content['footer']['date'] }}</p>
                <p class="text-white">Company: {{ $content['footer']['company'] }}</p>
                <p class="text-white">Address: {{ $content['footer']['address'] }}</p>
                <p class="text-white">Phone: {{ $content['footer']['phone'] }}</p>
                <p class="text-white">Email: {{ $content['footer']['email'] }}</p>
            </div>
        </div>
    @endsection
