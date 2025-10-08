<!-- Advanced Upload Modal with File Editing -->
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">
                    <i class="fas fa-cloud-upload-alt mr-2"></i>Upload & Edit Files
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- File Selection Area -->
                <div id="fileSelectionArea" class="upload-zone">
                    <div class="upload-dropzone" id="uploadDropzone" style="cursor: pointer;">
                        <div class="dropzone-content text-center">
                            <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-3"></i>
                            <h4>Drag & Drop Files Here</h4>
                            <p class="text-muted">or click anywhere in this area to browse files</p>
                            <button type="button" class="btn btn-primary" id="browseFilesBtn">
                                <i class="fas fa-folder-open mr-2"></i>Browse Files
                            </button>
                        </div>
                        <input type="file" id="fileUploadInput" multiple accept="image/*,application/pdf,.doc,.docx,.txt" style="display: none;">
                    </div>
                </div>

                <!-- File Preview and Editing Area -->
                <div id="fileEditingArea" style="display: none;">
                    <div class="row">
                        <!-- File List -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-list mr-2"></i>Selected Files
                                    </h6>
                                </div>
                                <div class="card-body p-0">
                                    <div id="selectedFilesList" class="list-group list-group-flush">
                                        <!-- Selected files will be populated here -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- File Editor -->
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0" id="currentFileTitle">
                                        <i class="fas fa-edit mr-2"></i>Edit File
                                    </h6>
                                    <div class="btn-group btn-group-sm" role="group" id="editorActions">
                                        <!-- Editor action buttons will be populated based on file type -->
                                    </div>
                                </div>
                                <div class="card-body" id="fileEditorContainer">
                                    <!-- Image Editor -->
                                    <div id="imageEditor" style="display: none;">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="image-editor-canvas">
                                                    <canvas id="imageCanvas" style="max-width: 100%; border: 1px solid #ddd;"></canvas>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="editor-controls">
                                                    <h6>Image Controls</h6>

                                                    <!-- Resize Controls -->
                                                    <div class="control-group mb-3">
                                                        <label class="form-label">Dimensions</label>
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <input type="number" class="form-control form-control-sm" id="imageWidth" placeholder="Width">
                                                            </div>
                                                            <div class="col-6">
                                                                <input type="number" class="form-control form-control-sm" id="imageHeight" placeholder="Height">
                                                            </div>
                                                        </div>
                                                        <div class="form-check mt-2">
                                                            <input class="form-check-input" type="checkbox" id="maintainAspectRatio" checked>
                                                            <label class="form-check-label" for="maintainAspectRatio">
                                                                Maintain aspect ratio
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <!-- Crop Controls -->
                                                    <div class="control-group mb-3">
                                                        <label class="form-label">Crop Presets</label>
                                                        <div class="btn-group-vertical w-100" role="group">
                                                            <button type="button" class="btn btn-outline-secondary btn-sm crop-preset" data-ratio="1:1">Square (1:1)</button>
                                                            <button type="button" class="btn btn-outline-secondary btn-sm crop-preset" data-ratio="16:9">Widescreen (16:9)</button>
                                                            <button type="button" class="btn btn-outline-secondary btn-sm crop-preset" data-ratio="4:3">Standard (4:3)</button>
                                                            <button type="button" class="btn btn-outline-secondary btn-sm crop-preset" data-ratio="free">Free Crop</button>
                                                        </div>
                                                    </div>

                                                    <!-- Quality Controls -->
                                                    <div class="control-group mb-3">
                                                        <label class="form-label">Quality</label>
                                                        <input type="range" class="form-range" id="imageQuality" min="10" max="100" value="90">
                                                        <div class="d-flex justify-content-between">
                                                            <small class="text-muted">Low</small>
                                                            <small class="text-muted">High</small>
                                                        </div>
                                                    </div>

                                                    <!-- Image Actions -->
                                                    <div class="control-group">
                                                        <div class="btn-group w-100" role="group">
                                                            <button type="button" class="btn btn-outline-primary btn-sm" id="rotateLeft" title="Rotate Left">
                                                                <i class="fas fa-undo"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-primary btn-sm" id="rotateRight" title="Rotate Right">
                                                                <i class="fas fa-redo"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-primary btn-sm" id="flipHorizontal" title="Flip Horizontal">
                                                                <i class="fas fa-arrows-alt-h"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-primary btn-sm" id="flipVertical" title="Flip Vertical">
                                                                <i class="fas fa-arrows-alt-v"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Document Preview -->
                                    <div id="documentPreview" style="display: none;">
                                        <div class="document-info text-center">
                                            <i class="fas fa-file-alt fa-4x text-muted mb-3"></i>
                                            <h5 id="documentName">Document Name</h5>
                                            <p class="text-muted" id="documentSize">File Size</p>
                                            <div class="document-actions mt-3">
                                                <button type="button" class="btn btn-outline-primary btn-sm" id="renameDocument">
                                                    <i class="fas fa-edit mr-2"></i>Rename
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- File Info Editor -->
                                    <div id="fileInfoEditor" class="mt-3">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="fileName">File Name</label>
                                                    <input type="text" class="form-control" id="fileName" placeholder="Enter file name">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="fileDescription">Description (Optional)</label>
                                                    <input type="text" class="form-control" id="fileDescription" placeholder="File description">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upload Progress -->
                <div id="uploadProgressArea" style="display: none;">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-3">
                                <i class="fas fa-cloud-upload-alt mr-2"></i>Uploading Files...
                            </h6>
                            <div id="uploadProgressList">
                                <!-- Progress bars for individual files will be added here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-outline-primary" id="addMoreFiles" style="display: none;">
                    <i class="fas fa-plus mr-2"></i>Add More Files
                </button>
                <button type="button" class="btn btn-primary" id="startUpload" style="display: none;">
                    <i class="fas fa-upload mr-2"></i>Upload Files
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.upload-zone {
    min-height: 300px;
}

.upload-dropzone {
    border: 2px dashed #cbd5e0;
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
    min-height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.upload-dropzone:hover,
.upload-dropzone.dragover {
    border-color: #4299e1;
    background-color: #f7fafc;
}

.dropzone-content {
    pointer-events: none;
}

.selected-file-item {
    padding: 0.75rem;
    border-bottom: 1px solid #e2e8f0;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.selected-file-item:hover {
    background-color: #f8f9fa;
}

.selected-file-item.active {
    background-color: #e3f2fd;
    border-left: 3px solid #2196f3;
}

.file-thumbnail {
    width: 40px;
    height: 40px;
    object-fit: cover;
    border-radius: 4px;
}

.image-editor-canvas {
    position: relative;
    text-align: center;
}

.editor-controls {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 6px;
}

.control-group {
    border-bottom: 1px solid #e9ecef;
    padding-bottom: 1rem;
}

.control-group:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.form-range {
    width: 100%;
}

.crop-preset {
    margin-bottom: 0.25rem;
}

.upload-progress-item {
    margin-bottom: 1rem;
    padding: 0.75rem;
    background-color: #f8f9fa;
    border-radius: 6px;
}

.progress {
    height: 6px;
}

@media (max-width: 768px) {
    .modal-xl {
        max-width: 95%;
    }

    .editor-controls {
        margin-top: 1rem;
    }
}
</style>
