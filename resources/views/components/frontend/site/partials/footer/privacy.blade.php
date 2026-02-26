{{-- Footer Privacy Links Component --}}
<div class="footer-content p-3">
    <h4 class="text-white">{{ __('frontend.footer.privacy') }}</h4>
    <ul class="list-unstyled">
        <li><a href="{{ route('pages', 'privacy') }}" class="text-white-50">{{ __('frontend.footer.privacy_policy') }}</a>
        </li>
        <li><a href="{{ route('pages', 'terms') }}" class="text-white-50">{{ __('frontend.footer.terms') }}</a></li>
        <li><a href="{{ route('pages', 'student_agreement') }}"
                class="text-white-50">{{ __('frontend.footer.student_agreement') }}</a></li>
    </ul>
</div>
