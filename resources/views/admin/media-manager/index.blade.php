@extends('adminlte::page')

@section('title', 'Media Manager')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Media Manager</h1>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#uploadModal">
            <i class="fas fa-upload"></i> Upload Files
        </button>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Folders</h3>
                </div>
                <div class="card-body">
                    <div class="folder-tree">
                        <ul class="list-unstyled">
                            <li><i class="fas fa-folder"></i> media</li>
                            <li><i class="fas fa-folder"></i> images</li>
                            <li><i class="fas fa-folder"></i> documents</li>
                            <li><i class="fas fa-folder"></i> videos</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Files</h3>
                </div>
                <div class="card-body">
                    <div class="media-grid">
                        <div class="text-center p-4">
                            <i class="fas fa-folder-open fa-3x text-muted"></i>
                            <p class="mt-3 text-muted">Media manager is ready for use</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="uploadForm" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Upload Files</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="files">Select Files</label>
                            <input type="file" class="form-control-file" id="files" name="files[]" multiple>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .media-grid {
            min-height: 400px;
        }
        .folder-tree li {
            padding: 5px 0;
            cursor: pointer;
        }
        .folder-tree li:hover {
            background-color: #f8f9fa;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            console.log('Media Manager loaded');

            $('#uploadForm').on('submit', function(e) {
                e.preventDefault();
                // Handle file upload
                console.log('File upload form submitted');
            });
        });
    </script>
@stop
