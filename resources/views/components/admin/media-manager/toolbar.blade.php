<!-- Modern Toolbar -->
<div class="media-toolbar">
    <div class="row align-items-center">
        <div class="col-md-6">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-modern" id="uploadBtn">
                    <i class="fas fa-cloud-upload-alt mr-2"></i>Upload Files
                </button>
                <button type="button" class="btn btn-modern" id="createFolderBtn">
                    <i class="fas fa-folder-plus mr-2"></i>New Folder
                </button>
                <button type="button" class="btn btn-modern" id="refreshBtn">
                    <i class="fas fa-sync-alt mr-2"></i>Refresh
                </button>
            </div>
            <!-- File Statistics -->
            <div class="ml-3 d-inline-block">
                <small class="text-light disk-status" id="fileStats">
                    <i class="fas fa-info-circle mr-1"></i>Loading...
                </small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="d-flex justify-content-end align-items-center">
                <div class="search-container mr-3">
                    <div class="input-group">
                        <input type="text" class="form-control search-input" id="searchInput" placeholder="Search files...">
                        <div class="input-group-append">
                            <button class="btn btn-search" type="button" id="searchBtn">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-modern active" id="gridViewBtn" title="Grid View">
                        <i class="fas fa-th"></i>
                    </button>
                    <button type="button" class="btn btn-modern" id="listViewBtn" title="List View">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Progress Bar (Hidden by default) -->
    <div class="row mt-3" id="uploadProgressContainer" style="display: none;">
        <div class="col-12">
            <div class="progress" style="height: 4px; border-radius: 2px;">
                <div class="progress-bar bg-success" role="progressbar" id="uploadProgressBar" style="width: 0%"></div>
            </div>
            <small class="text-light mt-1 d-block" id="uploadStatusText">Uploading files...</small>
        </div>
    </div>
</div>
