@extends('layouts.app')

@php
    $aboutContent = [
        [
            'heading' => 'Florida Online Security Training',
            'content' => 'Florida Online Security Training is an online training provider that specializes in providing high-quality security officer training to individuals seeking to become licensed security officers in the state of Florida. Our mission is to provide comprehensive and accessible training programs that equip our students with the skills and knowledge needed to succeed in their careers.'
        ],
        [
            'heading' => 'Wide Range of Training Programs',
            'content' => 'At Florida Online Security Training, we offer a wide range of training programs to meet the needs of our students. Our courses include unarmed security officer training, armed security officer training, and CPR and first aid training. We also offer a self-paced online training program that allows our students to complete the training on their own schedule.'
        ],
        [
            'heading' => 'Experienced Instructors',
            'content' => 'Our training programs are designed and taught by experienced instructors who are experts in their field. They bring years of industry experience and knowledge to the classroom, providing our students with practical insights and real-world examples.'
        ],
        [
            'heading' => 'Accessible and Convenient',
            'content' => 'We understand that becoming a licensed security officer in Florida can be a challenging and time-consuming process. That\'s why we strive to make our training programs as accessible and convenient as possible. Our online training program allows students to complete the training from the comfort of their own home, while our flexible scheduling options make it easy to fit the training around their busy schedules.'
        ],
        [
            'heading' => 'Accreditation and Compliance',
            'content' => 'At Florida Online Security Training, we are committed to providing our students with the highest quality training programs available. We are accredited by the Florida Department of Agriculture and Consumer Services Division of Licensing, and our courses fulfill the state\'s requirements for security officer licensing.'
        ],
        [
            'heading' => 'Excellent Customer Support',
            'content' => 'We also offer excellent customer support to our students. Our customer support team is available via phone and email during normal business hours, and we also have a technical support hotline for technical issues. Our website includes a knowledge base and frequently asked questions to help answer common questions.'
        ],
        [
            'heading' => 'Start or Advance Your Career',
            'content' => 'Florida Online Security Training is the premier online training provider for individuals seeking to become licensed security officers in Florida. Our comprehensive training programs, experienced instructors, and excellent customer support make us the best choice for anyone looking to start or advance their career in security. Contact us today to learn more about our training programs and how we can help you achieve your career goals.'
        ],
    ];
@endphp

@section('content')
    @include('frontend.partials.breadcrumbs')
    @include('frontend.panels.3panel-design')
    @include('frontend.panels.split_panel')
@stop
