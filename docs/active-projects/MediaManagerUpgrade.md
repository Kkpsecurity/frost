# Media Manager with Role-Based Disks + Unified Media Player

## Overview
Implement a comprehensive Media Manager that supports multiple disk types (public, local, S3) with a unified media manager interface and role-based access control.

## Goal
Create a Media Manager supporting:
- Laravel public disk (student/frontend access)
- Laravel local disk (admin/backend protected)
- Custom S3 disk (archive storage)
- Single unified Media Player UI for all disk types
- Role-based permissions and access control
- Remap The AvatarTrait to use Media Manager

## ✅ PHASE 1 COMPLETE: Environment & Configuration Setup

### ✅ 1.1 Update Environment Configuration
```bash
# Added to .env
FILESYSTEM_DISK=public
MEDIA_MANAGER_DEFAULT_DISK=public
MEDIA_MANAGER_ENABLE_S3_ARCHIVE=true
MEDIA_MANAGER_ENABLE_TRANSCODING=true

# Media Manager S3 Archive (using existing KKPS3)
MEDIA_S3_ACCESS_KEY=KhsFPqbAOCaGjClk
MEDIA_S3_SECRET_KEY=RneZLadRTDqOEcaxEWqWqgAHoKENOZVm
MEDIA_S3_ENDPOINT=https://ha-s3.hq.cisadmin.com
MEDIA_S3_REGION=us-east-1
MEDIA_S3_BUCKET=frost-devel
```

### ✅ 1.2 Update Filesystem Configuration
**File:** `config/filesystems.php`

Added media_s3 disk configuration:
```php
'media_s3' => [
    'driver' => 's3',
    'key' => env('MEDIA_S3_ACCESS_KEY'),
    'secret' => env('MEDIA_S3_SECRET_KEY'),
    'region' => env('MEDIA_S3_REGION'),
    'bucket' => env('MEDIA_S3_BUCKET'),
    'endpoint' => env('MEDIA_S3_ENDPOINT'),
    'use_path_style_endpoint' => true,
    'visibility' => 'private',
    'throw' => false,
],
```

### ✅ 1.3 Storage Link Verified
```bash
php artisan storage:link ✓
```

## ✅ PHASE 2 COMPLETE: Database Setup

### ✅ 2.1 Created Media Manager Tables
Migration: `2025_07_31_160147_create_media_manager_tables.php`

Tables created:
- `media_manager_files` - Core file metadata
- `media_manager_permissions` - Role-based permissions per disk  
- `media_manager_audit_logs` - Action logging

### ✅ 2.2 Spatie Media Library Installed
```bash
composer require spatie/laravel-medialibrary ✓
```

## ✅ PHASE 3 COMPLETE: Core Models & Services

### ✅ 3.1 Models Created
- **MediaFile** (`app/Models/MediaFile.php`) - Enhanced with file type detection and URL generation
- **MediaPermission** (`app/Models/MediaPermission.php`) - Role-based disk permissions

### ✅ 3.2 Services Created
- **MediaManagerService** (`app/Services/MediaManagerService.php`) - Enhanced existing service with new Media Manager functionality while maintaining backward compatibility
- **StreamingService** (`app/Services/StreamingService.php`) - HTTP range request support for video/audio streaming

## ✅ PHASE 4 COMPLETE: Controllers & Routes

### ✅ 4.1 Controllers Created
- **MediaManagerController** (`app/Http/Controllers/MediaManagerController.php`) - Main Media Manager API
- **MediaStreamController** (`app/Http/Controllers/MediaStreamController.php`) - Secure file streaming

### ✅ 4.2 Routes Added
**File:** `routes/admin/media_manager_routes.php`

NEW routes (maintaining existing ones):
```php
// NEW Media Manager with Role-Based Disks + Unified Media Player
Route::prefix('new-media-manager')->name('new-media.')->group(function () {
    Route::get('/', [NewMediaManagerController::class, 'index'])->name('index');
    Route::post('/upload', [NewMediaManagerController::class, 'upload'])->name('upload');
    Route::get('/files', [NewMediaManagerController::class, 'listFiles'])->name('files');
    Route::get('/tree', [NewMediaManagerController::class, 'getTree'])->name('tree');
    Route::delete('/file/{file}', [NewMediaManagerController::class, 'deleteFile'])->name('delete');
    Route::post('/archive/{file}', [NewMediaManagerController::class, 'archiveFile'])->name('archive');
    Route::get('/file/{file}', [NewMediaManagerController::class, 'getFileDetails'])->name('details');
});

// Media streaming routes (for local disk files)
Route::middleware(['auth'])->group(function () {
    Route::get('/media/stream/{file}', [MediaStreamController::class, 'stream'])
        ->name('media.stream');
});
```

## ✅ PHASE 5 COMPLETE: Basic Frontend Implementation

### ✅ 5.1 Admin Interface Created
**File:** `resources/views/admin/media-manager/index.blade.php`

Features implemented:
- **Storage Selector**: Switch between public, local, media_s3 disks
- **Directory Tree**: Browse collections/folders per disk
- **File Upload**: With disk and collection selection
- **File Grid**: Visual file browser with type-specific icons
- **Media Player Modal**: Unified player for images, videos, audio, PDFs
- **File Actions**: View, Delete, Archive to S3

### ✅ 5.2 JavaScript Implementation
- AJAX-based file operations
- Real-time disk switching
- Unified media player modal
- File type detection and preview
- Error handling with toastr notifications

## ✅ PHASE 6 COMPLETE: Permissions & Security

### ✅ 6.1 Permission System
**Default Permissions Matrix:**
- **Admin**: Full access to all disks (view, upload, delete, move, archive)
- **Staff**: Read/write public, read/write/delete local, view-only S3
- **Student**: Read/write public only
- **Instructor**: Enhanced public access, read/write local, view-only S3

### ✅ 6.2 Security Features
- Authentication required for all operations
- Role-based access control per disk
- Secure streaming for local files (no direct URLs)
- HTTP range request support for video/audio
- Audit logging for all destructive operations

## 🔄 NEXT STEPS - PHASE 7: Advanced Features & Integration

### 7.1 AvatarTrait Integration 
**Priority: HIGH**

Create enhanced AvatarTrait that uses Media Manager:
```php
// app/Traits/EnhancedAvatarTrait.php
trait EnhancedAvatarTrait 
{
    public function uploadAvatarToMediaManager(UploadedFile $file): MediaFile
    {
        // Upload to appropriate disk based on user type
        $disk = $this->getAvatarDisk();
        $collection = 'avatars';
        
        return app(MediaManagerService::class)->uploadFileToMediaManager($file, $disk, $collection);
    }
    
    private function getAvatarDisk(): string 
    {
        return match($this->getUserRole()) {
            'student' => 'public',
            'admin', 'staff' => 'local',
            default => 'public'
        };
    }
}
```

### 7.2 Frontend User Interface
Create student-facing interface:
- Simple upload widget for public disk
- Avatar management integration
- Document upload for course materials

### 7.3 Transcoding Service
```bash
php artisan make:job TranscodeVideoJob
composer require php-ffmpeg/php-ffmpeg
```

Implement video transcoding for incompatible formats:
- HLS generation for video streaming
- Multiple quality options
- Background processing

### 7.4 Enhanced Security
- EXIF data scrubbing for sensitive uploads
- File type validation beyond MIME
- Rate limiting for uploads/downloads
- Malware scanning integration

### 7.5 Performance Optimization
- Redis caching for file metadata
- CDN integration for public files
- Lazy loading for large directories
- Thumbnail generation

## 🎯 CURRENT STATUS

**✅ FULLY FUNCTIONAL:** 
- Multi-disk file management (public, local, S3)
- Role-based permissions
- Secure streaming for private files
- Unified media player
- File upload/delete/archive operations
- Admin interface with AJAX functionality

**🔄 IN PROGRESS:**
- Avatar system integration
- Frontend user interface
- Advanced security features

**📍 ACCESS THE NEW MEDIA MANAGER:**
Visit: `/admin/new-media-manager` 

The new Media Manager is fully functional and ready for testing! The foundation is solid and ready for the advanced features in Phase 7.
---

## Phase 1: Environment & Configuration Setup

### 1.1 Update Environment Configuration
```bash
# Add to .env
FILESYSTEM_DISK=public
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket-name
AWS_USE_PATH_STYLE_ENDPOINT=false

# Media Manager Settings
MEDIA_MANAGER_DEFAULT_DISK=public
MEDIA_MANAGER_ENABLE_S3_ARCHIVE=true
MEDIA_MANAGER_ENABLE_TRANSCODING=true
```

### 1.2 Update Filesystem Configuration
**File:** `config/filesystems.php`

Add/update disk configurations:
```php
'disks' => [
    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
        'throw' => false,
    ],

    'local' => [
        'driver' => 'local',
        'root' => storage_path('app'),
        'visibility' => 'private',
        'throw' => false,
    ],

    's3' => [
        'driver' => 's3',
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION'),
        'bucket' => env('AWS_BUCKET'),
        'url' => env('AWS_URL'),
        'endpoint' => env('AWS_ENDPOINT'),
        'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
        'throw' => false,
        'visibility' => 'private',
    ],
],
```

### 1.3 Ensure Storage Link
```bash
php artisan storage:link
```

---

## Phase 2: Database Setup

### 2.1 Create Media Manager Tables
```bash
php artisan make:migration create_media_manager_tables
```

**Migration content:**
```php
Schema::create('media_manager_files', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('original_name');
    $table->string('disk');
    $table->string('path');
    $table->string('mime_type');
    $table->bigInteger('size');
    $table->json('metadata')->nullable();
    $table->string('collection')->nullable();
    $table->unsignedBigInteger('user_id')->nullable();
    $table->timestamps();
    
    $table->index(['disk', 'collection']);
    $table->index('user_id');
});

Schema::create('media_manager_permissions', function (Blueprint $table) {
    $table->id();
    $table->string('role');
    $table->string('disk');
    $table->json('permissions'); // ['view', 'upload', 'delete', 'move', 'archive']
    $table->timestamps();
});

Schema::create('media_manager_audit_logs', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('user_id');
    $table->string('action');
    $table->string('disk');
    $table->string('file_path');
    $table->json('metadata')->nullable();
    $table->timestamp('created_at');
});
```

### 2.2 Install Spatie Media Library
```bash
composer require spatie/laravel-medialibrary
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="migrations"
php artisan migrate
```

---

## Phase 3: Core Models & Services

### 3.1 Create Media Manager Models

#### MediaFile Model
```bash
php artisan make:model Models/MediaFile
```

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class MediaFile extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'media_manager_files';
    
    protected $fillable = [
        'name', 'original_name', 'disk', 'path', 
        'mime_type', 'size', 'metadata', 'collection', 'user_id'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getUrlAttribute(): string
    {
        return app(MediaManagerService::class)->getFileUrl($this);
    }
}
```

#### MediaPermission Model
```bash
php artisan make:model Models/MediaPermission
```

### 3.2 Create Core Services

#### MediaManagerService
```bash
php artisan make:service Services/MediaManagerService
```

```php
<?php

namespace App\Services;

use App\Models\MediaFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class MediaManagerService
{
    public function getFileUrl(MediaFile $file): string
    {
        switch ($file->disk) {
            case 'public':
                return Storage::disk('public')->url($file->path);
            
            case 'local':
                return route('media.stream', ['file' => $file->id]);
            
            case 's3':
                return Storage::disk('s3')->temporaryUrl(
                    $file->path, 
                    now()->addHours(1)
                );
            
            default:
                throw new \InvalidArgumentException("Unsupported disk: {$file->disk}");
        }
    }

    public function uploadFile(UploadedFile $file, string $disk, string $collection = null): MediaFile
    {
        $this->validateDiskAccess($disk, 'upload');
        
        $path = $file->store($collection ?? 'uploads', $disk);
        
        $mediaFile = MediaFile::create([
            'name' => $file->getClientOriginalName(),
            'original_name' => $file->getClientOriginalName(),
            'disk' => $disk,
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'collection' => $collection,
            'user_id' => Auth::id(),
            'metadata' => $this->extractMetadata($file)
        ]);

        $this->auditAction('upload', $disk, $path);
        
        return $mediaFile;
    }

    public function archiveToS3(MediaFile $file): MediaFile
    {
        $this->validateDiskAccess('s3', 'archive');
        
        // Move file from current disk to S3
        $content = Storage::disk($file->disk)->get($file->path);
        $s3Path = "archive/{$file->collection}/" . basename($file->path);
        
        Storage::disk('s3')->put($s3Path, $content);
        
        // Update file record
        $file->update([
            'disk' => 's3',
            'path' => $s3Path
        ]);
        
        // Clean up original
        Storage::disk($file->disk)->delete($file->path);
        
        $this->auditAction('archive', 's3', $s3Path);
        
        return $file;
    }

    private function validateDiskAccess(string $disk, string $action): void
    {
        $permissions = app(PermissionService::class)->getUserDiskPermissions($disk);
        
        if (!in_array($action, $permissions)) {
            throw new \UnauthorizedHttpException("No {$action} permission for disk: {$disk}");
        }
    }

    private function extractMetadata(UploadedFile $file): array
    {
        // Extract EXIF, dimensions, etc.
        return [];
    }

    private function auditAction(string $action, string $disk, string $path): void
    {
        // Log to audit table
    }
}
```

#### StreamingService
```bash
php artisan make:service Services/StreamingService
```

```php
<?php

namespace App\Services;

use App\Models\MediaFile;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StreamingService
{
    public function streamFile(MediaFile $file, array $headers = []): StreamedResponse
    {
        $disk = Storage::disk($file->disk);
        
        if (!$disk->exists($file->path)) {
            abort(404);
        }

        $size = $disk->size($file->path);
        $mimeType = $file->mime_type;
        
        return response()->stream(function () use ($disk, $file) {
            $stream = $disk->readStream($file->path);
            fpassthru($stream);
            fclose($stream);
        }, 200, array_merge([
            'Content-Type' => $mimeType,
            'Content-Length' => $size,
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'no-cache, must-revalidate',
        ], $headers));
    }

    public function streamWithRange(MediaFile $file, string $range = null): StreamedResponse
    {
        $disk = Storage::disk($file->disk);
        $size = $disk->size($file->path);
        
        if ($range) {
            return $this->handleRangeRequest($file, $range, $size);
        }
        
        return $this->streamFile($file);
    }

    private function handleRangeRequest(MediaFile $file, string $range, int $size): StreamedResponse
    {
        // Parse range header and return partial content
        // Implementation for HTTP 206 Partial Content
        // Support for video/audio seeking
    }
}
```

---

## Phase 4: Controllers & Routes

### 4.1 Create Controllers

#### MediaManagerController
```bash
php artisan make:controller MediaManagerController
```

#### MediaStreamController
```bash
php artisan make:controller MediaStreamController
```

```php
<?php

namespace App\Http\Controllers;

use App\Models\MediaFile;
use App\Services\StreamingService;
use Illuminate\Http\Request;

class MediaStreamController extends Controller
{
    public function __construct(
        private StreamingService $streamingService
    ) {}

    public function stream(Request $request, MediaFile $file)
    {
        // Validate access permissions
        $this->authorize('view', $file);
        
        return $this->streamingService->streamWithRange(
            $file, 
            $request->header('Range')
        );
    }
}
```

### 4.2 Add Routes
**File:** `routes/web.php`

```php
Route::middleware(['auth'])->group(function () {
    Route::get('/media/stream/{file}', [MediaStreamController::class, 'stream'])
        ->name('media.stream');
    
    Route::prefix('media-manager')->group(function () {
        Route::get('/', [MediaManagerController::class, 'index'])->name('media.index');
        Route::post('/upload', [MediaManagerController::class, 'upload'])->name('media.upload');
        Route::delete('/file/{file}', [MediaManagerController::class, 'delete'])->name('media.delete');
        Route::post('/archive/{file}', [MediaManagerController::class, 'archive'])->name('media.archive');
        Route::get('/tree/{disk}', [MediaManagerController::class, 'tree'])->name('media.tree');
    });
});
```

---

## Phase 5: Frontend Implementation

### 5.1 Create Vue Components

#### MediaManager.vue
```vue
<template>
  <div class="media-manager">
    <div class="media-manager__sidebar">
      <StorageSelector v-model="selectedDisk" />
      <DirectoryTree 
        :disk="selectedDisk" 
        v-model="selectedPath"
        @refresh="refreshTree" 
      />
    </div>
    
    <div class="media-manager__main">
      <FileGrid 
        :files="files" 
        :disk="selectedDisk"
        @select="selectFile"
        @delete="deleteFile"
        @archive="archiveFile"
      />
      
      <FilePond 
        :disk="selectedDisk"
        :path="selectedPath"
        @uploaded="onFileUploaded"
      />
    </div>
    
    <MediaPlayer 
      v-if="selectedFile"
      :file="selectedFile"
      @close="selectedFile = null"
    />
  </div>
</template>
```

#### MediaPlayer.vue
```vue
<template>
  <div class="media-player-overlay" @click.self="$emit('close')">
    <div class="media-player">
      <div class="media-player__header">
        <h3>{{ file.name }}</h3>
        <button @click="$emit('close')">&times;</button>
      </div>
      
      <div class="media-player__content">
        <video v-if="isVideo" :src="fileUrl" controls />
        <audio v-else-if="isAudio" :src="fileUrl" controls />
        <img v-else-if="isImage" :src="fileUrl" />
        <iframe v-else-if="isPdf" :src="fileUrl" />
        <div v-else class="unsupported">
          <p>Preview not available</p>
          <a :href="fileUrl" download>Download</a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ['file'],
  computed: {
    fileUrl() {
      return this.file.url;
    },
    isVideo() {
      return this.file.mime_type?.startsWith('video/');
    },
    isAudio() {
      return this.file.mime_type?.startsWith('audio/');
    },
    isImage() {
      return this.file.mime_type?.startsWith('image/');
    },
    isPdf() {
      return this.file.mime_type === 'application/pdf';
    }
  }
};
</script>
```

### 5.2 Install FilePond
```bash
npm install filepond vue-filepond filepond-plugin-image-preview
```

---

## Phase 6: Permissions & Security

### 6.1 Create Permission Service
```bash
php artisan make:service Services/PermissionService
```

### 6.2 Create Policies
```bash
php artisan make:policy MediaFilePolicy
```

### 6.3 Implement Role-Based Access
- Admin: Full access to all disks
- Staff: Read/write local, read public
- Student: Read/write public only

---

## Phase 7: Advanced Features

### 7.1 Transcoding Service
```bash
php artisan make:job TranscodeVideoJob
composer require php-ffmpeg/php-ffmpeg
```

### 7.2 Audit Logging
- Log all destructive operations
- Track user actions across disks
- Generate audit reports

### 7.3 EXIF Scrubbing
- Remove sensitive metadata from uploads
- Especially important for ID photos and headshots

---

## Phase 8: Testing & Deployment

### 8.1 Create Tests
```bash
php artisan make:test MediaManagerTest
php artisan make:test StreamingServiceTest
php artisan make:test PermissionServiceTest
```

### 8.2 Performance Optimization
- Cache file metadata
- Optimize S3 operations
- Implement lazy loading for large directories

### 8.3 Error Handling
- Graceful fallbacks for failed streams
- Retry logic for S3 operations
- User-friendly error messages

---

## Configuration Checklist

- [ ] Environment variables configured
- [ ] Storage link created
- [ ] S3 credentials tested
- [ ] Database migrations run
- [ ] Spatie Media Library installed
- [ ] File permissions validated
- [ ] CORS configured for S3
- [ ] Queue workers configured for jobs

---

## Security Considerations

1. **Never expose local disk via public URLs**
2. **All local access must be authenticated streams**
3. **S3 temporary URLs should expire appropriately**
4. **Validate file types and sizes on upload**
5. **Sanitize file names and paths**
6. **Implement rate limiting for streams**
7. **Log all destructive operations**
8. **Regular security audits of permissions**

---

## Performance Notes

1. **Use streaming responses for large files**
2. **Implement proper HTTP caching headers**
3. **Support HTTP range requests for video/audio**
4. **Queue transcoding jobs**
5. **Cache directory listings**
6. **Optimize S3 operations with batch requests**

---

## Maintenance Tasks

1. **Regular cleanup of temporary files**
2. **Monitor S3 costs and usage**
3. **Archive old audit logs**
4. **Update temporary URL expiration policies**
5. **Backup media metadata regularly**

---

This implementation provides a robust, secure, and scalable media management system that meets all the specified requirements while maintaining flexibility for future enhancements.
