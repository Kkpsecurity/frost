@extends('adminlte::page')

@section('title', 'Media Manager - File Upload')

@section('content_header')
    <h1>Media Manager</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Admin Center</a></li>
            <li class="breadcrumb-item active" aria-current="page">Media Manager</li>
        </ol>
    </nav>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-image"></i> Image Upload
                    </h3>
                </div>
                <div class="card-body">
                    <form id="imageUploadForm">
                        @csrf
                        <x-admin.file-pond-upload
                            name="profile_image"
                            id="profile_image"
                            type="image"
                            label="Profile Image"
                            help="Upload your profile image (JPEG, PNG, GIF, WebP up to 5MB)"
                            :required="true"
                            max-file-size="5MB"
                            :allow-image-crop="true"
                        />

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Upload Image
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-alt"></i> Document Upload
                    </h3>
                </div>
                <div class="card-body">
                    <form id="documentUploadForm">
                        @csrf
                        <x-admin.file-pond-upload
                            name="documents"
                            id="documents"
                            type="document"
                            label="Documents"
                            help="Upload documents (PDF, DOC, DOCX, TXT up to 25MB)"
                            :multiple="true"
                            max-files="5"
                            max-file-size="25MB"
                        />

                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-upload"></i> Upload Documents
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cloud-upload-alt"></i> General File Upload
                    </h3>
                </div>
                <div class="card-body">
                    <form id="generalUploadForm">
                        @csrf
                        <x-admin.file-pond-upload
                            name="general_files"
                            id="general_files"
                            type="file"
                            label="General Files"
                            help="Upload any type of files (up to 10MB each)"
                            :multiple="true"
                            max-files="10"
                            max-file-size="10MB"
                        />

                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-upload"></i> Upload Files
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Usage Instructions
                    </h3>
                </div>
                <div class="card-body">
                    <h5>FilePond Component Usage:</h5>
                    <pre><code>&lt;x-admin.file-pond-upload
    name="field_name"
    id="unique_id"
    type="image|document|video|file"
    label="Display Label"
    help="Help text"
    :required="true|false"
    :multiple="true|false"
    max-file-size="5MB"
    max-files="10"
    :allow-image-crop="true|false"
    accepted-file-types="image/jpeg,image/png"
/&gt;</code></pre>

                    <h5 class="mt-4">Available Types:</h5>
                    <ul>
                        <li><strong>image</strong> - JPEG, PNG, GIF, WebP with preview and optional cropping</li>
                        <li><strong>document</strong> - PDF, DOC, DOCX, TXT files</li>
                        <li><strong>video</strong> - MP4, WebM, AVI, MOV files</li>
                        <li><strong>file</strong> - General file upload with custom types</li>
                    </ul>

                    <h5 class="mt-4">JavaScript Events:</h5>
                    <ul>
                        <li><code>filepond:added</code> - Fired when a file is added</li>
                        <li><code>filepond:processed</code> - Fired when a file is successfully uploaded</li>
                        <li><code>filepond:removed</code> - Fired when a file is removed</li>
                    </ul>

                    <h5 class="mt-4">Storage Configuration:</h5>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Current Storage:</strong>
                        @if(env('FILEPOND_USE_S3', false))
                            AWS S3 ({{ env('FILEPOND_S3_DISK', 's3') }})
                        @else
                            Local Storage
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    @vite('resources/css/filepond.css')
@stop

@section('js')
    @vite('resources/js/filepond.js')

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle form submissions
        document.getElementById('imageUploadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            handleFormSubmit(this, 'Image');
        });

        document.getElementById('documentUploadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            handleFormSubmit(this, 'Documents');
        });

        document.getElementById('generalUploadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            handleFormSubmit(this, 'Files');
        });

        function handleFormSubmit(form, type) {
            const fileInput = form.querySelector('.filepond-input');
            const pond = fileInput.filePondInstance;

            if (pond && pond.getFiles().length > 0) {
                // Process all files
                pond.processFiles().then(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: type + ' uploaded successfully',
                        timer: 3000,
                        showConfirmButton: false
                    });
                }).catch((error) => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Upload Failed',
                        text: 'Failed to upload ' + type.toLowerCase(),
                    });
                });
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Files Selected',
                    text: 'Please select files to upload',
                });
            }
        }

        // Listen for FilePond events
        document.addEventListener('filepond:processed', function(e) {
            console.log('File processed:', e.detail);
        });

        document.addEventListener('filepond:removed', function(e) {
            console.log('File removed:', e.detail);
        });

        document.addEventListener('filepond:added', function(e) {
            console.log('File added:', e.detail);
        });
    });
    </script>
@stop
