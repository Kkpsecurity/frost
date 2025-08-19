@extends('layouts.app')

@section('styles')
    <style>
        /*--------------------------------*/
        /* 18. Faq page Area
                /*--------------------------------*/

        .faq-page {
            padding: 60px 0px 60px 0px;
        }

        .faq-page .section-headline {
            margin-bottom: 20px;
        }

        .faq-page .section-headline h3 {
            margin-bottom: 20px;
            color: var(--frost-light-color);
            text-shadow: 0px 0px 5px rgba(0, 0, 0, 0.5);
        }

        .faq-page .section-headline p {
            color: var(--frost-light-color);
            font-size: 16px;
        }

        .faq-page .all-faq-text {
            margin-bottom: 20px;
        }

        .business-faq {
            padding: 25px 20px 10px 30px;
            border-radius: 5px;
        }

        .business-faq h3 {
            color: var(--frost-white-color);
        }

        .business-faq p {
            color: var(--frost-light-color);
        }

        .single-faq h4 {
            background: var(--frost-primary-color);
            color: var(--frost-white-color);
            padding: 12px 20px;
            margin-bottom: 30px;
            font-weight: 500;
            font-size: 22px;
            border-radius: 5px;
        }

        .faq-full {
            margin-bottom: 40px;
            display: block;
        }

        .panel-default {
            border: none;
        }

        .faq-details .panel-heading {
            padding: 0;
        }

        .panel-default>.panel-heading {
            border: medium none;
            color: #444;
        }

        .panel-default>.panel-heading+.panel-collapse>.panel-body {
            border: 1px solid var(--frost-primary-color);
            background: var(--frost-primary-color);
        }

        .faq-full .panel-default>.panel-heading,
        .faq-full .panel {
            background: var(--frost-primary-color);
            border-radius: 5px;
        }

        .panel-body {
            min-height: 100px;
            padding: 15px 15px 40px 45px;
        }

        .panel-body p {
            color: var(--frost-light-color);
            font-size: 16px;
            height: auto;
            margin-bottom: 0px;
        }

        .panel-body p:last-child {
            margin-bottom: 0px;
        }

        .check-title {
            color: var(--frost-white-color);
            font-size: 16px;
            margin-bottom: 0;
            text-transform: capitalize;
            background: var(--frost-primary-color);
            border: 1px solid var(--frost-primary-color);
            margin-bottom: 0px;
            border-radius: 5px;
        }

        .check-title a {
            color: var(--frost-white-color);
            display: block;
            font-weight: 500;
            padding: 10px 10px 10px 40px;
            text-decoration: none;
            border-radius: 5px;
        }

        .faq-details a span.acc-icons {
            position: relative;
        }

        .faq-details a span.acc-icons::before {
            color: var(--frost-white-color);
            content: "\e61a ";
            font-family: themify;
            font-size: 16px;
            left: -26px;
            line-height: 39px;
            position: absolute;
            text-align: center;
            top: -10px;
        }

        .faq-details a.active span.acc-icons::before {
            color: var(--frost-info-color);
            content: "\e622";
            font-family: themify;
            font-size: 16px;
            left: -26px;
            line-height: 39px;
            position: absolute;
            text-align: center;
            top: -10px;
        }

        .faq-details .panel-heading h4 a.active {
            color: var(--frost-info-color);
        }

        .panel-group {
            margin-bottom: 0px;
        }
    </style>
@stop

@php $faqArray = []; @endphp

@php
    $faqArray = [
        'title' => 'Frequently Asked Questions',
        'description' => '',
    
        'account' => [
            'title' => 'Learning Platform',
            'description' => 'Our learning platform offers a variety of educational resources. If you have questions, please see our FAQs below.',
            'faqs' => [
                [
                    'question' => 'How to create a new account?',
                    'answer' => 'Go to the registration page, fill in the required details, and click "Sign up". A confirmation email will be sent to your inbox. Click the link in the email to verify your account.',
                ],
                [
                    'question' => 'What should I do if I forget my password?',
                    'answer' => 'Click on the "Forgot password" link on the login page, enter your email address and follow the instructions sent to your email to reset your password.',
                ],
                [
                    'question' => 'How can I enroll in a course?',
                    'answer' => 'Navigate to the "Courses" page, select a course that interests you, and click on "Enroll now". Follow the prompts to complete enrollment.',
                ],
                [
                    'question' => 'Can I take more than one course at a time?',
                    'answer' => 'No, Our Course is designed to be taken one at a time. You can only enroll in one course at a time.',
                ],
                [
                    'question' => 'What should I do if I have a technical problem?',
                    'answer' => 'If you encounter any technical issues, please contact our support team through the "Contact us" page.',
                ],
                [
                    'question' => 'How can I update my profile information?',
                    'answer' => 'To update your profile information, log into your account, navigate to "My Profile", make any necessary changes, and click "Save".',
                ],
                [
                    'question' => 'Are there any system requirements to use this platform?',
                    'answer' => 'Our platform works best with the latest versions of Chrome, Firefox, and Safari. Ensure you have a stable internet connection for an uninterrupted learning experience.',
                ],
                [
                    'question' => 'Can I get a refund for a course?',
                    'answer' => 'Please refer to our "Refund Policy" for detailed information on refunds.',
                ],
                [
                    'question' => 'How can I contact a course instructor?',
                    'answer' => 'You can contact a course instructor through the messaging feature in the course dashboard. If the feature is not available, please reach out to our support team for assistance.',
                ],
                [
                    'question' => 'Are there any prerequisites for courses?',
                    'answer' => 'Prerequisites vary depending on the course. The course description page will have detailed information on prerequisites, if any.',
                ],
            ],
        ],
    
        'course' => [
            'title' => 'Taking the Class',
            'description' => 'Our courses are designed to be easy to follow and understand. If you have questions, please see our FAQs below.',
            'faqs' => [
                [
                    'question' => 'What is STG Online Firearm Training, and what courses does it offer?',
                    'answer' => 'STG Online Firearm Training is a digital platform that provides online firearm training for D and G licenses in the state of Florida. Our courses are designed for individuals interested in obtaining these licenses for personal or professional purposes.',
                ],
                [
                    'question' => 'Is STG Online Firearm Training accredited, and do the courses fulfill Florida\'s requirements for D and G licenses?',
                    'answer' => 'Yes, STG Online Firearm Training is fully licensed, and our courses meet all the state of Florida requirements for D and G licenses.',
                ],
                [
                    'question' => 'How long does it take to complete an online firearm training course with STG?',
                    'answer' => 'The duration of our courses depends on the course type and individual pace. However, our courses are designed to be convenient and flexible for all learners.',
                ],
                [
                    'question' => 'Can I access the course materials and complete the training at my own pace?',
                    'answer' => 'Absolutely, STG Online Firearm Training offers a self-paced learning approach. You can access the course materials and complete the training according to your own schedule.',
                ],
                [
                    'question' => 'What kind of support does STG Online Firearm Training provide during the training process?',
                    'answer' => 'We provide live support throughout your training journey. Our team is always ready to help and guide you whenever you need assistance.',
                ],
                [
                    'question' => 'Is there a fee for taking a course with STG Online Firearm Training?',
                    'answer' => 'Yes, there is a fee for taking our online courses. The fees help us maintain the quality of our training and the resources we provide.',
                ],
                [
                    'question' => 'Does STG Online Firearm Training offer any discounts or promotions?',
                    'answer' => 'We occasionally offer promotions or discounts. Keep an eye on our website or subscribe to our newsletter for the latest updates.',
                ],
                [
                    'question' => 'Are the course materials provided by STG Online Firearm Training available in languages other than English?',
                    'answer' => 'Currently, our course materials are provided only in English.',
                ],
                [
                    'question' => 'What is the process for receiving my certification after completing a training course with STG Online Firearm Training?',
                    'answer' => 'After completing our training course, you will receive a certificate of completion, which you can use to apply for your D or G license in Florida.',
                ],
                [
                    'question' => 'How long is my certification valid, and what are the requirements for renewing it?',
                    'answer' => 'The validity of the certification depends on the guidelines set by the state of Florida. Please refer to the relevant Florida statutes for details about license renewal.',
                ],
                [
                    'question' => 'Why am I being removed from the classroom?',
                    'answer' => 'The system is designed to monitor your presence in the classroom. If you are browsing or using another application, and the classroom is not in focus (meaning the classroom browser is not selected), it is considered as if you are not present. To avoid being removed, ensure that the classroom browser is active and selected.',
                ],
            ],
        ],
    ];
@endphp

@section('content')
    @include('frontend.partials.breadcrumbs')
    <div class="faq-page frost-secondary-bg area-padding">
        <div class="container ">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="section-headline frost-primary-bg text-center p-3">
                        <h3>{{ isset($faqArray['title']) ? $faqArray['title'] : '' }}</h3>
                        <p>{{ isset($faqArray['description']) ? $faqArray['description'] : '' }}</p>
                    </div>
                </div>
            </div>

            @foreach ($faqArray as $index => $category)
                @if (is_array($category) && isset($category['title'], $category['description'], $category['faqs']))
                    <div class="row">
                        ...
                        <div class="faq-full">
                            <div class="single-faq">
                                <h4>{{ $category['title'] }}</h4>
                            </div>
                            <div class="faq-details p-0">
                                <div class="accordion " id="accordion{{ $index }}">
                                    @foreach ($category['faqs'] as $faqIndex => $faq)
                                        @if (is_array($faq) && isset($faq['question'], $faq['answer']))
                                            <div class="panel">
                                                <div class="panel-header"
                                                    id="heading{{ $index }}{{ $faqIndex + 1 }}">
                                                    <h4 class="check-title">
                                                        <button class="accordion-button" type="button"
                                                            data-bs-toggle="collapse"
                                                            data-bs-target="#check{{ $index }}{{ $faqIndex + 1 }}"
                                                            aria-expanded="false"
                                                            aria-controls="check{{ $index }}{{ $faqIndex + 1 }}">
                                                            <span class="acc-icons"></span>{{ $faq['question'] }}
                                                        </button>
                                                    </h4>
                                                </div>
                                                <div id="check{{ $index }}{{ $faqIndex + 1 }}" class="collapse"
                                                    aria-labelledby="heading{{ $index }}{{ $faqIndex + 1 }}"
                                                    data-bs-parent="#accordion{{ $index }}">
                                                    <div class="panel-body">
                                                        <p>{{ $faq['answer'] }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
@stop
