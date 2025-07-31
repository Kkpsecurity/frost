<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Files</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="fileInput">Select Files</label>
                        <input type="file" class="form-control-file" id="fileInput" name="files[]" multiple>
                    </div>
                    <div class="form-group">
                        <label for="collectionInput">Collection/Folder</label>
                        <input type="text" class="form-control" id="collectionInput" name="collection"
                            placeholder="uploads">
                    </div>
                    <div class="progress" style="display: none;" id="uploadProgress">
                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="uploadSubmit">Upload</button>
            </div>
        </div>
    </div>
</div>
