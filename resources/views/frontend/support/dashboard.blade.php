@extends('layouts.app')

@section('page-title', $content['title'])
@section('page-keywords', $content['keywords'])
@section('page-description', $content['description'])

@php
    $contentArray = [
        1 => [
            'title' => 'Online Security Training Program',
            'menu_item' => 'Security Training Program',
            'sub_title' => 'Florida Class D and G License',
            'desc' => 'A Comprehensive Online Training',
            'support_text' => 'Our Online Security Training Program offers comprehensive courses for individuals seeking the Florida Class D Security License and the Armed Statewide Firearms Class G License. Take advantage of this opportunity to embark on a rewarding career in the security industry. Enroll in our Online Security Training Program today and obtain the necessary licenses to pursue your professional goals.',
        ],
        2 => [
            'title' => 'Online Unarmed Class D Security License Course',
            'menu_item' => 'Class D License Course',
            'sub_title' => 'Essential for Security Career',
            'desc' => 'Training Program for Class D License',
            'support_text' => 'The Florida Class D Security License Course is a comprehensive training program designed to equip individuals with the knowledge and skills required to obtain their Class D Security License in Florida. By the end of the course, students will have a thorough understanding of their roles and responsibilities as security professionals.',
        ],
        3 => [
            'title' => 'Florida Class G Statewide Firearms Course',
            'menu_item' => 'Class G Firearms Course',
            'sub_title' => 'Become an Armed Security Officer',
            'desc' => 'Training Program for Class G License',
            'support_text' => 'The Florida Class G Statewide Firearms Course is a comprehensive training program designed to provide individuals with the necessary knowledge and skills to obtain their Class G Firearms License in Florida. Completing the Florida Class G Statewide Firearms Course enables individuals to apply for their Class G Firearms License, authorizing them to carry and use firearms in their security and investigative duties.',
        ],
        4 => [
            'title' => 'About The Security Training Group',
            'menu_item' => 'About STG',
            'sub_title' => 'Florida’s Premier Security Training Provider',
            'desc' => 'Professional Security Training since 1998',
            'support_text' => 'Welcome to The Security Training Group, Florida’s premier choice for top-tier security training. Our dedicated team, which includes U.S. Military Veterans, has been setting high standards in training since 1998. Join us to embark on a successful career in the security industry.',
        ],
        5 => [
            'title' => 'Refund and Cancellation Policy',
            'menu_item' => 'Refund Policy',
            'sub_title' => 'Know Before You Enroll',
            'desc' => 'Detailed Refund and Cancellation Guidelines',
            'support_text' => 'Our policy provides clarity on refunds and cancellations for our online courses. Before enrolling, understand that refunds aren’t granted once courses commence. It’s essential to be informed and ensure your commitment before starting your training journey.',
        ],
        6 => [
            'title' => 'K Partners for Range Training',
            'menu_item' => 'K Partners Training',
            'sub_title' => 'Top-notch Firearms Training',
            'desc' => 'Affiliated Training in accordance with Florida Statutes',
            'support_text' => 'Our collaboration with approved K Partners ensures that students receive the best range training. These independent entities are certified and follow all state regulations, assuring a quality and legal training experience.',
        ],
        7 => [
            'title' => 'Webcam Troubleshooting Guide',
            'menu_item' => 'Webcam Troubleshooting',
            'sub_title' => 'Resolving Webcam Connection Issues',
            'desc' => 'Different browsers may present unique challenges when it comes to webcam functionality. However, most browsers offer a reset feature for the webcam, which typically resolves about 80% of connectivity issues. In this guide, we\'ll walk you through the steps to reset your webcam settings when encountering difficulties with the screen share interface.',
            'support_text' => '
               <li> Firefox: To reset your webcam settings, click on the camera icon located in the address bar. From the dropdown, select the option to "Always Allow" access to your webcam.</li>
               <li> Chrome: In Chrome, you can reset your webcam settings by clicking on the lock icon situated to the left of the address bar. Here, you\'ll find both the camera and microphone icons. After adjusting your settings, remember to refresh the page for changes to take effect.</li>
               <li> Edge: For Edge users, click on the lock icon in the address bar, then navigate to "Permissions for this site." Here, you\'ll find options to reset your camera settings.</li>
            ',
        ],
    ];

@endphp

@section('content')
    <div class="support-area page-wrapper frost-secondary-bg">
        <aside id="sidebar">
            <div class="button-container clearfix">
                <div class="menu-toggle flaticon-menu10"></div>
            </div>
            <div class="menu-box">
                <br>
                <div class="logo text-right">
                    <a href="{{ url('support') }}">
                        <img src="{{ asset('assets/img/logo/support-logo.png') }}" alt="Support" />
                    </a>
                </div>
                <nav class="sticky-menu">
                    <ul>
                        @foreach ($contentArray as $key => $content)
                            <li class="{{ $key === 1 ? 'current' : '' }}">
                                <a href="#{{ \Str::lower($content['title']) }}">
                                    {{ $content['menu_item'] }} <span class="fa fa-arrow-right"></span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            </div>
        </aside>
        <div id="content-section" class="pb-5">
            @foreach ($contentArray as $key => $content)
                <section class="section {{ \Str::lower($content['title']) }}" id="{{ \Str::lower($content['title']) }}">
                    <div class="sec-title">
                        <h2><span class="icon fa fa-arrow-right "></span> {{ $content['title'] }}</h2>
                    </div>
                    <br>
                    <div class="sec-content">
                        <h2>{{ $content['sub_title'] }}</h2>
                        <h3>{{ $content['desc'] }}</h3>
                        <div class="separator"></div>
                        <br>
                        <p>{!! $content['support_text'] !!}</p>
                    </div>
                </section>
            @endforeach
        </div>
    </div>
@endsection
