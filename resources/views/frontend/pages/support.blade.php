@extends('layouts.app')
@php

    $faqs = [];
    // Adding a new section for "Preparing for Your Course" details

    $content = [
        'title' => 'User Account Dashboard',
        'intro' => 'Manage your account settings, personal information, and preferences.',
        'sections' => [
            'personalInfo' => [
                'title' => 'Personal Information',
                'description' => 'Update your name, contact details, and other personal information.',
                'fields' => [
                    'fullName' => 'Full Name',
                    'email' => 'Email Address',
                    'phone' => 'Phone Number',
                    'dob' => 'Date of Birth',
                    // Add any other personal information fields here
                ],
            ],
            'avatar' => [
                'title' => 'Avatar',
                'description' => 'Change your profile picture or avatar.',
                'fields' => [
                    'currentAvatar' => 'Current Avatar',
                    'uploadAvatar' => 'Upload New Avatar',
                ],
            ],
            'accountSecurity' => [
                'title' => 'Account Security',
                'description' => 'Settings related to the security of your account, including password changes and two-factor authentication.',
                'fields' => [
                    'changePassword' => 'Change Password',
                    'twoFactorAuth' => 'Two-Factor Authentication',
                    // Add any other security fields here
                ],
            ],
            'preferences' => [
                'title' => 'Preferences',
                'description' => 'Customize your account to better fit your needs, including language, notification preferences, etc.',
                'fields' => [
                    'language' => 'Language',
                    'notifications' => 'Notification Settings',
                    // Add any other preference fields here
                ],
            ],
            // Add additional sections as needed
        ],
    ];
@endphp
@section('content')
    @include('frontend.partials.breadcrumbs')
    <div class="container py-3 secondary-bg">
        <div class="row">
            <div class="col-12 p-3">
                <h2>{{ $content['title'] }}</h2>
                <p>{{ $content['intro'] }}</p>

                @foreach ($content['sections'] as $sectionKey => $section)
                    <section id="{{ $sectionKey }}">
                        <h3>{{ $section['title'] }}</h3>
                        <p>{{ $section['description'] }}</p>
                        <ul class="list-group">
                            @foreach ($section['fields'] as $fieldKey => $fieldName)
                                <li class="list-group-item">
                                    <strong>{{ $fieldName }}</strong>
                                    {{-- Add descriptive text or data --}}
                                    <span>Information or data related to {{ strtolower($fieldName) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endforeach
            </div>
        </div>
    </div>
@stop
