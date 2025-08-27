# Media Upload Task - Implementation Plan

## Overview
Complete implementation plan for the Media Manager upload functionality, starting with Public disk storage and extending to Private and S3 storage systems.

## Current Status
- ✅ Dark mode styling completed
- ✅ Tab functionality working
- ✅ Upload button clicks working
- ✅ File loading API integration working
- ✅ Upload functionality implemented (MediaController updated)
- ✅ Backend validation rules implemented
- ✅ File storage directories created
- ✅ MediaFileService updated for new folder structure
- ✅ JavaScript uploadFiles function implemented
- ✅ Sidebar layout fixed (only shows folder structure)
- ✅ Upload tools moved to access level bar (header)
- ✅ Folder-specific upload buttons implemented
- ❌ File grid population needs completion (populateFileGrid stubbed)
- ❌ Upload testing pending

---

## PUBLIC DISK IMPLEMENTATION

### 1. Directory Structure
```
/public/storage/media/
├─ images/
├─ documents/
├─ assets/
└─ validations/
    ├─ headshots/
    └─ idcard/
```

### 2. File Type Rules by Folder

#### images/
- **Allowed Extensions:** `.jpg`, `.jpeg`, `.png`, `.gif`
- **Purpose:** General user-uploaded pictures (course illustrations, student galleries)
- **Max Size:** 10MB per file
- **Validation:** Image dimensions, file integrity

#### documents/
- **Allowed Extensions:** `.pdf`, `.doc`, `.docx`
- **Purpose:** PDFs and Word docs (assignments, handouts, reports)
- **Max Size:** 25MB per file
- **Validation:** Document structure, virus scanning

#### assets/
- **Allowed Extensions:** `.css`, `.js`, `.json`
- **Purpose:** Static site files used by the front-end (stylesheets, scripts, JSON config)
- **Max Size:** 5MB per file
- **Validation:** Syntax checking, security scanning

#### validations/headshots/ & validations/idcard/
- **Allowed Extensions:** `.jpg`, `.jpeg`, `.png`
- **Purpose:** Student verification uploads
  - `headshots/` - Selfie-style photos for ID matching
  - `idcard/` - Scans or photos of government-issued IDs
- **Max Size:** 8MB per file
- **Validation:** Image quality, face detection (headshots), text recognition (ID cards)

---

## IMPLEMENTATION PHASES

### Phase 1: Backend Infrastructure

#### 1.1 Create Upload Controller
**File:** `app/Http/Controllers/Admin/MediaUploadController.php`

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MediaUploadController extends Controller
{
    private $publicDiskRules = [
        'images' => [
            'extensions' => ['jpg', 'jpeg', 'png', 'gif'],
            'max_size' => 10240, // 10MB in KB
            'mime_types' => ['image/jpeg', 'image/png', 'image/gif']
        ],
        'documents' => [
            'extensions' => ['pdf', 'doc', 'docx'],
            'max_size' => 25600, // 25MB in KB
            'mime_types' => ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']
        ],
        'assets' => [
            'extensions' => ['css', 'js', 'json'],
            'max_size' => 5120, // 5MB in KB
            'mime_types' => ['text/css', 'application/javascript', 'application/json']
        ],
        'validations' => [
            'extensions' => ['jpg', 'jpeg', 'png'],
            'max_size' => 8192, // 8MB in KB
            'mime_types' => ['image/jpeg', 'image/png']
        ]
    ];

    public function upload(Request $request)
    {
        // Implementation details in Phase 1.2
    }

    public function listFiles(Request $request)
    {
        // Implementation details in Phase 1.3
    }

    public function deleteFile(Request $request)
    {
        // Implementation details in Phase 1.4
    }
}
```

#### 1.2 Upload Method Implementation
- Validate disk type and folder
- Check file type against folder rules
- Generate unique filename with timestamp
- Store file with proper directory structure
- Return success/error response with file details

#### 1.3 File Listing Method
- List files in specified disk/folder
- Return file metadata (name, size, modified date, URL)
- Support pagination for large directories
- Include thumbnail generation for images

#### 1.4 File Management Methods
- Delete files with permission checks
- Move/rename files between folders
- Bulk operations support

### Phase 2: Frontend JavaScript Implementation

#### 2.1 Upload Function
**File:** `resources/views/components/admin/media-manager/scripts.blade.php`

```javascript
function uploadFiles(files, diskId) {
    const formData = new FormData();
    const currentFolder = getCurrentFolder(diskId);
    
    // Add files to FormData
    Array.from(files).forEach((file, index) => {
        formData.append(`files[${index}]`, file);
    });
    
    // Add metadata
    formData.append('disk', diskId);
    formData.append('folder', currentFolder);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    
    // Show progress indicator
    showUploadProgress(diskId);
    
    // AJAX upload
    $.ajax({
        url: '/admin/media/upload',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        xhr: function() {
            const xhr = new XMLHttpRequest();
            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    updateUploadProgress(diskId, (e.loaded / e.total) * 100);
                }
            });
            return xhr;
        },
        success: function(response) {
            handleUploadSuccess(response, diskId);
        },
        error: function(xhr) {
            handleUploadError(xhr, diskId);
        }
    });
}
```

#### 2.2 Progress Indicators
- Real-time upload progress bars
- File-by-file progress tracking
- Error handling with user-friendly messages
- Success notifications with file details

#### 2.3 File Grid Population
```javascript
function populateFileGrid(files, diskId) {
    const container = $(`#${diskId}-files`);
    container.empty();
    
    if (!files || files.length === 0) {
        container.html('<div class="no-files">No files found</div>');
        return;
    }
    
    const grid = $('<div class="file-grid"></div>');
    
    files.forEach(file => {
        const fileCard = createFileCard(file, diskId);
        grid.append(fileCard);
    });
    
    container.append(grid);
}

function createFileCard(file, diskId) {
    // Create file card with thumbnail, name, size, actions
    // Include preview, download, delete options
    // Handle different file types appropriately
}
```

### Phase 3: Route Configuration

#### 3.1 Admin Routes
**File:** `routes/admin.php`

```php
Route::prefix('media')->name('media.')->group(function () {
    Route::post('upload', [MediaUploadController::class, 'upload'])->name('upload');
    Route::get('list', [MediaUploadController::class, 'listFiles'])->name('list');
    Route::delete('file/{disk}/{path}', [MediaUploadController::class, 'deleteFile'])->name('delete');
    Route::post('move', [MediaUploadController::class, 'moveFile'])->name('move');
});
```

### Phase 4: Validation & Security

#### 4.1 File Validation Rules
```php
private function getValidationRules($disk, $folder)
{
    $rules = $this->publicDiskRules[$folder] ?? null;
    
    if (!$rules) {
        throw new ValidationException('Invalid folder specified');
    }
    
    return [
        'files.*' => [
            'required',
            'file',
            'max:' . $rules['max_size'],
            'mimes:' . implode(',', $rules['extensions'])
        ]
    ];
}
```

#### 4.2 Security Measures
- CSRF token validation
- File type verification (not just extension)
- Virus scanning integration
- User permission checks
- Rate limiting for uploads
- File size limits per user/session

### Phase 5: Error Handling & User Experience

#### 5.1 Client-Side Validation
- Pre-upload file type checking
- Size validation before upload
- Drag & drop visual feedback
- Real-time validation messages

#### 5.2 Server-Side Error Responses
```php
// Standardized error response format
return response()->json([
    'success' => false,
    'message' => 'Upload failed',
    'errors' => [
        'file1.jpg' => ['File too large'],
        'file2.pdf' => ['Invalid file type']
    ]
], 422);
```

#### 5.3 Progress & Feedback
- Upload progress indicators
- Success/error notifications
- File processing status
- Batch operation feedback

---

## PRIVATE DISK IMPLEMENTATION

### Directory Structure
```
/storage/app/private/media/
├─ student-files/
├─ admin-documents/
├─ course-materials/
└─ backups/
```

### Key Differences from Public
- Files not directly accessible via URL
- Requires authentication for access
- Download through controller with permission checks
- More restrictive file types
- Enhanced logging and audit trails

---

## S3 DISK IMPLEMENTATION

### Directory Structure
```
s3-bucket/media/
├─ public-assets/
├─ user-uploads/
├─ course-content/
└─ archived/
```

### Key Features
- CDN integration
- Automatic backup
- Versioning support
- Cross-region replication
- Lifecycle policies

---

## TESTING STRATEGY

### Unit Tests
- File validation logic
- Upload controller methods
- Security checks
- Error handling

### Integration Tests
- End-to-end upload flow
- File listing and retrieval
- Permission checks
- Cross-disk operations

### Browser Tests
- Drag & drop functionality
- Progress indicators
- Error message display
- Responsive design

---

## DEPLOYMENT CHECKLIST

### Pre-Deployment
- [ ] Create storage directories
- [ ] Set proper permissions
- [ ] Configure file size limits
- [ ] Test all file types
- [ ] Verify security measures

### Post-Deployment
- [ ] Monitor upload performance
- [ ] Check error logs
- [ ] Verify file accessibility
- [ ] Test backup procedures
- [ ] Performance optimization

---

## FUTURE ENHANCEMENTS

### Phase 6: Advanced Features
- Image resizing and optimization
- Video thumbnail generation
- File versioning
- Bulk operations UI
- Integration with external services

### Phase 7: Performance Optimization
- Chunked uploads for large files
- Background processing
- CDN integration
- Caching strategies
- Database optimization

---

## TECHNICAL NOTES

### File Storage Paths
- Public: `/public/storage/media/{folder}/`
- Private: `/storage/app/private/media/{folder}/`
- S3: `s3://bucket/media/{folder}/`

### Database Schema
```sql
CREATE TABLE media_files (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    disk VARCHAR(50) NOT NULL,
    folder VARCHAR(100) NOT NULL,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    file_size BIGINT UNSIGNED NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    user_id BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_disk_folder (disk, folder),
    INDEX idx_user (user_id)
);
```

### Configuration Files
- Update `config/filesystems.php` for disk configurations
- Add validation rules to `config/media.php`
- Configure upload limits in `php.ini` and nginx/apache

---

This implementation plan provides a comprehensive roadmap for implementing the Media Upload functionality with proper validation, security, and user experience considerations.
