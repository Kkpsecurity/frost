<div class="card card-outline card-info">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-hdd"></i> Storage Configuration
        </h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <label for="storage-disk">Current Storage Disk:</label>
                <select id="storage-disk" class="form-control">
                    @if (isset($disks) && is_array($disks))
                        @foreach ($disks as $disk => $name)
                            <option value="{{ $disk }}"
                                {{ ($currentDisk ?? 'public') === $disk ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    @else
                        <option value="public" selected>Local Public Storage</option>
                        <option value="s3">Frost S3 Storage</option>
                    @endif
                </select>
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <button id="migrate-files" class="btn btn-warning mr-2">
                    <i class="fas fa-exchange-alt"></i> Migrate Legacy Files
                </button>
                <button id="refresh-stats" class="btn btn-info">
                    <i class="fas fa-sync"></i> Refresh Stats
                </button>
            </div>
        </div>
    </div>
</div>
