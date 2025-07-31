<div class="row no-gutters">
    <div class="col-md-3">
        <div class="directory-tree" id="s3Tree">
            <div class="tree-item active" data-path="/">
                <i class="fas fa-folder mr-2"></i>Root
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="upload-area" id="s3UploadArea" style="display: none;">
            <i class="fas fa-cloud fa-3x text-muted mb-3"></i>
            <h5>Drop files for S3 archive or click to upload</h5>
            <p class="text-muted">Long-term storage and backup</p>
            <input type="file" id="s3FileInput" multiple style="display: none;">
        </div>
        <div class="media-grid" id="s3Grid">
            <!-- Files will be loaded here -->
        </div>
    </div>
</div>
