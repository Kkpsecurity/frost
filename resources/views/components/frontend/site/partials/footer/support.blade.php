{{-- Footer Support Links Component --}}
<div class="footer-content p-3">
    <h4 class="text-white">{{ __('frontend.footer.support') }}</h4>
    <ul class="list-unstyled">
        <li><a href="{{ route('pages', 'support') }}" class="text-white-50">{{ __('frontend.footer.support_center') }}</a>
        </li>
        <li><a href="{{ route('pages', 'faqs') }}" class="text-white-50">{{ __('frontend.footer.faqs') }}</a></li>
        <li><a href="{{ route('pages', 'contact') }}" class="text-white-50">{{ __('frontend.footer.contact_us') }}</a>
        </li>
    </ul>
</div>
