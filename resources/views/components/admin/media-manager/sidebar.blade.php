<!-- Sidebar -->
<div class="col-md-3">
    <div class="card media-sidebar ">
        <div class="card-body">
            <div class="sidebar-section vh-100">
                <h6><i class="fas fa-folder-tree mr-2"></i>Directories</h6>
                <div class="directory-tree" id="directoryTree">
                    <!-- Dynamic directory listing will be populated here -->
                    <div class="tree-item" data-path="/media" style="cursor: pointer;">
                        <i class="fas fa-home mr-2 text-primary"></i>Media Root
                    </div>
                </div>
            </div>

            <div class="sidebar-section mt-4">
                <h6><i class="fas fa-info-circle mr-2"></i>Storage Info</h6>
                <div id="storageInfo" class="text-muted">
                    <small>Select a storage location to view details</small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .tree-item:hover {
        background-color: #a77200;
        cursor: pointer;
    }
</style>
