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
                <h2 class="text-white">Preparing for Your Course</h2>
                <h5 class="text-white-50">
                    Before enrolling in our weapons license course, here's what you need to know
                    and prepare:
                </h5>
            </div>
        </div>
        <div class="row" id="servicesContainer"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const servicesData = [{
            icon: "fas fa-user-shield", // FontAwesome icon class for User Shield
            title: "Live Class",
            description: "Welcome to our interactive live class environment. Attendance at scheduled sessions is crucial for optimal learning and engagement. While we offer offline classes for the completion of missed lessons, it's important to understand that these are supplementary and not intended for self-paced complete course learning. Active participation in live sessions is essential for a comprehensive educational experience."
        },
        {
            icon: "fas fa-upload", // FontAwesome icon class for Upload
            title: "Upload Identification Photos",
            description: "You will be asked to upload a photo of your ID and a photo of you for verification purposes."
        },
        {
            icon: "fas fa-video", // FontAwesome icon class for Webcam
            title: "Use of WebCam",
            description: "A webcam will be used to take a photo of you holding your ID and also to communicate with the instructor during live sessions."
        },
        {
            icon: "fas fa-question", // FontAwesome icon class for Question
            title: "Challenge Questions",
            description: "Throughout the course, you'll be asked challenge questions to verify your identity and ensure your active participation."
        },
        {
            icon: "fas fa-calendar-day", // FontAwesome icon class for Calendar Day
            title: "5-Day Course Duration",
            description: "The course spans over 5 days. Ensure you're available each day to gain the most from each session and actively participate."
        },
        {
            icon: "fas fa-pencil-alt", // FontAwesome icon class for Test (used pencil as a representation)
            title: "Final Test",
            description: "Taking the test will be the final step in the process. It becomes available once all lessons are completed."
        },
        {
            icon: "fas fa-certificate", // FontAwesome icon class for Certificate
            title: "Course Completion & Certificate",
            description: "Upon completing the course, you'll gain access to The DOA. Please note that the certificate will be available for printing after 24 hours after exam completion, as proof of your achievement."
        }

    ];

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
