 <div class="card card-primary card-outline card-tabs">
     <div class="card-header p-0 pt-1 border-bottom-0">
         <ul class="nav nav-tabs" id="upload-tabs" role="tablist">
             <li class="nav-item">
                 <a class="nav-link active" id="image-tab" data-toggle="pill" href="#image-upload" role="tab">
                     <i class="fas fa-image"></i> Image Upload
                 </a>
             </li>
             <li class="nav-item">
                 <a class="nav-link" id="document-tab" data-toggle="pill" href="#document-upload" role="tab">
                     <i class="fas fa-file-alt"></i> Document Upload
                 </a>
             </li>
             <li class="nav-item">
                 <a class="nav-link" id="student-tab" data-toggle="pill" href="#student-upload" role="tab">
                     <i class="fas fa-user-graduate"></i> Student Files
                 </a>
             </li>
             <li class="nav-item">
                 <a class="nav-link" id="general-tab" data-toggle="pill" href="#general-upload" role="tab">
                     <i class="fas fa-cloud-upload-alt"></i> General Upload
                 </a>
             </li>
         </ul>
     </div>
     <div class="card-body">
         <div class="tab-content" id="upload-tabContent">
             <!-- Image Upload Tab -->
             <div class="tab-pane fade show active" id="image-upload" role="tabpanel">
                 <form id="imageUploadForm">
                     @csrf
                     <input type="hidden" name="category" value="images">
                     <x-admin.media-manager.partials.file-pond-upload name="profile_image" id="profile_image" type="image"
                         label="Image Files" help="Upload image files (JPEG, PNG, GIF, WebP up to 5MB)"
                         :multiple="true" max-file-size="5MB" :allow-image-crop="true" />
                     <button type="submit" class="btn btn-primary">
                         <i class="fas fa-upload"></i> Upload Images
                     </button>
                 </form>
             </div>

             <!-- Document Upload Tab -->
             <div class="tab-pane fade" id="document-upload" role="tabpanel">
                 <form id="documentUploadForm">
                     @csrf
                     <input type="hidden" name="category" value="documents">
                     <x-admin.media-manager.partials.file-pond-upload name="documents" id="documents" type="document" label="Documents"
                         help="Upload documents (PDF, DOC, DOCX, TXT up to 25MB)" :multiple="true" max-files="10"
                         max-file-size="25MB" />
                     <button type="submit" class="btn btn-info">
                         <i class="fas fa-upload"></i> Upload Documents
                     </button>
                 </form>
             </div>

             <!-- Student Files Tab -->
             <div class="tab-pane fade" id="student-upload" role="tabpanel">
                 <div class="row">
                     <div class="col-md-6">
                         <h5><i class="fas fa-id-card"></i> Student Validations</h5>
                         <form id="validationUploadForm">
                             @csrf
                             <div class="form-group">
                                 <label for="user_id">Student ID</label>
                                 <input type="number" class="form-control" name="user_id" id="user_id" required>
                             </div>
                             <div class="form-group">
                                 <label for="validation_type">Validation Type</label>
                                 <select class="form-control" name="type" id="validation_type">
                                     <option value="validation">Validation Photo</option>
                                     <option value="id_front">ID Front</option>
                                     <option value="id_back">ID Back</option>
                                     <option value="selfie">Selfie</option>
                                 </select>
                             </div>
                             <x-admin.media-manager.partials.file-pond-upload name="validation_file" id="validation_file" type="image"
                                 label="Validation File" help="Upload validation image or PDF (up to 10MB)"
                                 max-file-size="10MB" />
                             <button type="submit" class="btn btn-success">
                                 <i class="fas fa-upload"></i> Upload Validation
                             </button>
                         </form>
                     </div>
                     <div class="col-md-6">
                         <h5><i class="fas fa-user-circle"></i> Student Avatars</h5>
                         <form id="avatarUploadForm">
                             @csrf
                             <div class="form-group">
                                 <label for="avatar_user_id">Student ID</label>
                                 <input type="number" class="form-control" name="user_id" id="avatar_user_id"
                                     required>
                             </div>
                             <x-admin.media-manager.partials.file-pond-upload name="avatar_file" id="avatar_file" type="image"
                                 label="Avatar Image" help="Upload avatar image (JPEG, PNG up to 5MB)"
                                 max-file-size="5MB" :allow-image-crop="true" />
                             <button type="submit" class="btn btn-primary">
                                 <i class="fas fa-upload"></i> Upload Avatar
                             </button>
                         </form>
                     </div>
                 </div>
             </div>

             <!-- General Upload Tab -->
             <div class="tab-pane fade" id="general-upload" role="tabpanel">
                 <form id="generalUploadForm">
                     @csrf
                     <div class="form-group">
                         <label for="upload_category">File Category</label>
                         <select class="form-control" name="category" id="upload_category" required>
                             @if (isset($categories) && is_array($categories))
                                 @foreach ($categories as $key => $name)
                                     <option value="{{ $key }}">{{ $name }}</option>
                                 @endforeach
                             @else
                                 <option value="images">Images</option>
                                 <option value="documents">Documents</option>
                                 <option value="students/validations">Student Validations</option>
                                 <option value="students/avatars">Student Avatars</option>
                                 <option value="general">General Files</option>
                             @endif
                         </select>
                     </div>
                     <x-admin.media-manager.partials.file-pond-upload name="general_files" id="general_files" type="file"
                         label="General Files" help="Upload any type of files (up to 25MB each)" :multiple="true"
                         max-files="10" max-file-size="25MB" />
                     <button type="submit" class="btn btn-success">
                         <i class="fas fa-upload"></i> Upload Files
                     </button>
                 </form>
             </div>
         </div>
     </div>
 </div>
