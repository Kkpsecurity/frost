<!-- Modern Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-cloud-upload-alt mr-2"></i>Upload Files
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="uploadForm">
                    <div class="form-group">
                        <label for="fileInput">Select Files</label>
                        <input type="file" class="form-control-file" id="fileInput" multiple>
                        <small class="form-text text-muted">Maximum file size: 50MB per file</small>
                    </div>
                    <div class="form-group">
                        <label for="collectionInput">Collection (Optional)</label>
                        <input type="text" class="form-control" id="collectionInput" placeholder="uploads">
                        <small class="form-text text-muted">Group files into collections for better organization</small>
                    </div>
                    <div class="progress" id="uploadProgress" style="display: none;">
                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-modern" id="uploadSubmit" disabled>
                    <i class="fas fa-upload mr-2"></i>Upload Files
                </button>
            </div>
        </div>
    </div>
</div>
