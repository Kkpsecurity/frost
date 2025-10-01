{{-- Payment Processing Page --}}
<x-frontend.site.site-wrapper :title="$content['title'] ?? 'Payment Processing'">
    <x-slot:head>
        <meta name="description" content="{{ $content['description'] ?? 'Complete your secure payment for course enrollment' }}">
        <meta name="keywords" content="{{ $content['keywords'] ?? 'payment processing, secure checkout, course enrollment' }}">
        <link rel="stylesheet" href="{{ asset('css/components/payment.css') }}">
    </x-slot:head>

    <x-frontend.site.partials.header />

    <div class="container-fluid m-0 p-0" style="min-height: 70vh;">
        <x-frontend.panels.payments.payflowpro :payment="$payment" :course="$course" :paymentConfig="$paymentConfig" />
    </div>

    <x-frontend.site.partials.footer />

</x-frontend.site.site-wrapper>
