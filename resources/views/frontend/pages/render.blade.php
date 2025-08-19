{{-- Site Home Page - Uses Site Layout Component --}}
<x-site.layout title="Welcome to {{ config('app.name') }}">
    <x-slot:head>
        <meta name="description" content="Professional security training and certification courses">
        <meta name="keywords" content="security, training, certification, cyber security">
    </x-slot:head>

    <x-site.partials.header />


    <x-slot:footer>
        <footer>
            <div class="container text-center">
                <div style="padding: 2rem 0; border-bottom: 1px solid #4b5563; margin-bottom: 2rem;">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; text-align: left;">
                        <div>
                            <h4 style="margin-bottom: 1rem;">{{ config('app.name') }}</h4>
                            <p style="color: #9ca3af;">Professional security training and certification platform.</p>
                        </div>
                        <div>
                            <h4 style="margin-bottom: 1rem;">Quick Links</h4>
                            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                <a href="#courses" style="color: #9ca3af; text-decoration: none;">Courses</a>
                                <a href="#about" style="color: #9ca3af; text-decoration: none;">About</a>
                                <a href="#contact" style="color: #9ca3af; text-decoration: none;">Contact</a>
                            </div>
                        </div>
                        <div>
                            <h4 style="margin-bottom: 1rem;">Support</h4>
                            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                <a href="#help" style="color: #9ca3af; text-decoration: none;">Help Center</a>
                                <a href="#faq" style="color: #9ca3af; text-decoration: none;">FAQ</a>
                                <a href="#privacy" style="color: #9ca3af; text-decoration: none;">Privacy Policy</a>
                            </div>
                        </div>
                    </div>
                </div>
                <p style="color: #9ca3af; margin: 0;">
                    © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </p>
            </div>
        </footer>
    </x-slot:footer>
</x-site.layout>
