@extends('layouts.app')

@php
    $terms = [
        [
            'title' => 'Use of LMS',
            'content' => 'The LMS is provided for educational and training purposes only. You may use the LMS solely for your own personal or business use, and not for any commercial purposes.',
        ],
        [
            'title' => 'User Content',
            'content' => 'The LMS may allow you to upload, submit, or otherwise make available content, such as text, images, or videos (“User Content”). You retain all rights in, and are solely responsible for, the User Content you make available through the LMS. You represent and warrant that you have all necessary rights to make the User Content available through the LMS, and that the User Content does not infringe any third party rights or violate any applicable laws.',
        ],
        [
            'title' => 'Intellectual Property',
            'content' => 'The LMS, including all content, materials, and trademarks, is owned by the Company or its licensors, and is protected by intellectual property laws. You may not copy, modify, distribute, sell, or otherwise exploit the LMS or any content or materials on the LMS without the Company’s prior written consent.',
        ],
        [
            'title' => 'Disclaimer of Warranties',
            'content' => 'The LMS is provided on an “as is” and “as available” basis, without any warranties of any kind, express or implied, including but not limited to warranties of merchantability, fitness for a particular purpose, or non-infringement. The Company does not warrant that the LMS will be error-free or uninterrupted, or that any defects will be corrected.',
        ],
        [
            'title' => 'Limitation of Liability',
            'content' => 'To the maximum extent permitted by law, the Company and its affiliates, officers, employees, agents, and licensors will not be liable for any direct, indirect, incidental, special, consequential, or exemplary damages, including but not limited to damages for loss of profits, goodwill, use, data, or other intangible losses, arising out of or in connection with the LMS or this Agreement.',
        ],
        [
            'title' => 'Indemnification',
            'content' => 'You agree to indemnify and hold the Company and its affiliates, officers, employees, agents, and licensors harmless from any claim or demand, including reasonable attorneys’ fees, made by any third party due to or arising out of your breach of this Agreement or your use of the LMS.',
        ],
        [
            'title' => 'Termination',
            'content' => 'The Company may terminate this Agreement and your access to the LMS at any time, without notice or liability, for any reason or no reason, including but not limited to if you breach this Agreement.',
        ],
        [
            'title' => 'Governing Law',
            'content' => 'This Agreement and any disputes arising out of or in connection with the LMS or this Agreement will be governed by and construed in accordance with the laws of the [State/Country] without regard to its conflict of law provisions.',
        ],
        [
            'title' => 'Entire Agreement',
            'content' => 'This Agreement constitutes the entire agreement between you and the Company regarding the LMS, and supersedes all prior or contemporaneous communications, agreements, or understandings, whether written or oral.',
        ],
    ];
@endphp

@section('content')

    @include('frontend.partials.breadcrumbs')

    <div class="terms-area frost-primary-bg area-padding-2">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="section-headline text-center">
                        <h3>Terms & Conditions</h3>
                        <p class="text-white-50">Read through our terms and conditions to understand the guidelines for using our services.</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="company-terms">
                        @foreach ($terms as $index => $term)
                            <div class="single-terms">
                                <h4><span class="number">{{ $index + 1 }}.</span> <span
                                        class="condition-text">{{ $term['title'] }}</span></h4>
                                <p>{{ $term['content'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
