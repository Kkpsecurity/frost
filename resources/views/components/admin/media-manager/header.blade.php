<!-- AdminLTE Card Header with Tabs -->
<div class="card-header p-0 bg-light text-dark dark-mode bg-dark border-bottom-0">
    <!-- Card Title -->
    <div class="d-flex justify-content-between align-items-center p-3 border-bottom border-secondary">
        <h3 class="card-title mb-0 text-primary">
            <i class="fas fa-cloud mr-2"></i>Media Manager
        </h3>
        <div class="d-flex align-items-center">
            <span class="badge bg-primary text-light px-3 py-2 mr-2">
                <i class="fas fa-database mr-1"></i><span id="currentDiskDisplay">Public</span> Storage
            </span>
            <div id="loadingIndicator" class="badge bg-secondary text-light px-3 py-2 d-none">
                <i class="fas fa-spinner fa-spin mr-1"></i>Loading...
            </div>
        </div>
    </div>

    <!-- Storage Type Tabs -->
    <ul class="nav nav-tabs nav-pills bg-light dark:bg-dark" role="tablist">
        <!-- Public Tab -->
        <li class="nav-item">
            <a class="nav-link active bg-white text-dark dark:bg-dark dark:text-light" href="#public" data-disk="public" role="tab">
                <i class="fas fa-globe mr-2"></i>Public
                <small class="d-block text-muted dark:text-gray-400">Images, Assets</small>
                <span class="disk-status-indicator" data-disk="public"></span>
            </a>
        </li>

        <!-- Private Tab -->
        @if(auth('admin')->check() && auth('admin')->user()->IsInstructor())
        <li class="nav-item">
            <a class="nav-link bg-white text-dark dark:bg-dark dark:text-light" href="#private" data-disk="local" role="tab">
                <i class="fas fa-shield-alt mr-2"></i>Private
                <small class="d-block text-muted dark:text-gray-400">Documents, Files</small>
                <span class="disk-status-indicator" data-disk="local"></span>
            </a>
        </li>
        @endif

        <!-- Archive S3 Tab -->
        @if(auth('admin')->check() && auth('admin')->user()->IsAdministrator())
        <li class="nav-item">
            <a class="nav-link bg-white text-dark dark:bg-dark dark:text-light" href="#s3" data-disk="s3" role="tab">
                <i class="fas fa-archive mr-2"></i>Archive S3
                <small class="d-block text-muted dark:text-gray-400">Long-term Storage</small>
                <span class="disk-status-indicator" data-disk="s3"></span>
            </a>
        </li>
        @endif
    </ul>

    <!-- Access Level Indicator -->
    <div class="p-3 bg-light dark:bg-dark border-top border-secondary">
        <i class="fas fa-user-shield mr-1 text-muted"></i>
        Access Level:
        @if(auth('admin')->check())
            @php $user = auth('admin')->user(); @endphp
            @if($user->IsSysAdmin())
                <span class="text-success font-weight-bold">System Admin</span> - Full access to all storage locations
            @elseif($user->IsAdministrator())
                <span class="text-success font-weight-bold">Admin</span> - Full access to all storage locations
            @elseif($user->IsSupport())
                <span class="text-info font-weight-bold">Support</span> - Admin access, including private storage
            @elseif($user->IsInstructor())
                <span class="text-warning font-weight-bold">Instructor</span> - Admin access, including private storage
            @else
                <span class="text-secondary font-weight-bold">Student/Guest</span> - Public read-only access
            @endif
        @else
            <span class="text-muted">Not authenticated</span>
        @endif
    </div>
</div>
