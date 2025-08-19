@props(['title' => 'Media Manager'])

<div class="media-manager-container">
    <!-- AdminLTE Card Structure -->
    <div class="card">
        <!-- Card Header with Tabs -->
        <x-admin.media-manager.header />

        <!-- Card Body -->
        <div class="card-body p-0">
            <!-- Main Content Area -->
            <x-admin.media-manager.content />
        </div>
    </div>

    <!-- Upload Modal -->
    <x-admin.media-manager.upload-modal />
</div>
