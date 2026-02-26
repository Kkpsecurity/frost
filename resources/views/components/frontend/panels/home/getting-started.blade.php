@push('component-styles')
    @vite(['resources/css/components/getting-started.css'])
@endpush

@php
    $manifestPath = public_path('build/manifest.json');
    $useVite = false;
    if (file_exists($manifestPath)) {
        $manifest = json_decode(file_get_contents($manifestPath), true);
        if (is_array($manifest) && array_key_exists('resources/css/components/getting-started.css', $manifest)) {
            $useVite = true;
        }
    }
@endphp


<div class="frost-secondary-bg py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="text-white">{{ __('frontend.home.preparing_title') }}</h2>
                <h5 class="text-white-50">
                    {{ __('frontend.home.preparing_subtitle') }}
                </h5>
            </div>
        </div>
        <div class="row" id="servicesContainer"></div>
    </div>
</div>

<script>
    @php
        $servicesData = [['icon' => 'fas fa-user-shield', 'title' => __('frontend.home.live_class_title'), 'description' => __('frontend.home.live_class_desc')], ['icon' => 'fas fa-upload', 'title' => __('frontend.home.upload_id_title'), 'description' => __('frontend.home.upload_id_desc')], ['icon' => 'fas fa-video', 'title' => __('frontend.home.webcam_title'), 'description' => __('frontend.home.webcam_desc')], ['icon' => 'fas fa-question', 'title' => __('frontend.home.challenge_title'), 'description' => __('frontend.home.challenge_desc')], ['icon' => 'fas fa-calendar-day', 'title' => __('frontend.home.duration_title'), 'description' => __('frontend.home.duration_desc')], ['icon' => 'fas fa-pencil-alt', 'title' => __('frontend.home.final_test_title'), 'description' => __('frontend.home.final_test_desc')], ['icon' => 'fas fa-certificate', 'title' => __('frontend.home.completion_title'), 'description' => __('frontend.home.completion_desc')]];
    @endphp
    document.addEventListener('DOMContentLoaded', function() {
        const servicesData = @json($servicesData);

        // Step 2: Loop over the data and generate the HTML
        const servicesContainer = document.getElementById("servicesContainer");

        if (!servicesContainer) {
            console.error('Services container not found!');
            return;
        }

        servicesData.forEach(service => {
            const serviceDiv = document.createElement('div');
            serviceDiv.className = 'col-md-6 col-sm-12 mb-4';

            serviceDiv.innerHTML = `
            <div class="support-services">
                <span class="top-icon"><i class="${service.icon}"></i></span>
                <span class="support-images d-inline-block">
                    <i class="${service.icon}"></i>
                </span>
                <div class="support-content ms-4">
                    <h4>${service.title}</h4>
                    <p class="truncated-text">${service.description}</p>
                </div>
            </div>
        `;


            // Step 3: Append the generated HTML to the DOM
            servicesContainer.appendChild(serviceDiv);
        });

        console.log('Getting Started component loaded successfully');
    });
</script>
