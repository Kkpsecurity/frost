# Media Configuration System - Usage Guide

The `config/media.php` file provides a comprehensive configuration system for organizing and managing all media assets in the FROST application.

## 🎯 Key Features

### 1. **Centralized Configuration**
- All media settings in one place
- Category-based organization
- File validation rules
- Security configurations

### 2. **Organized Directory Structure**
```
storage/app/public/media/
├── assets/          # Theme assets (images, icons, logos)
├── content/         # Educational content (courses, documents, videos)
├── user/            # User-generated content (avatars, uploads, certificates)
└── system/          # System files (cache, temp, backups)
```

### 3. **Enhanced MediaManager Class**
- Configuration-driven operations
- Automatic file validation
- Smart filename generation
- Category-specific helpers

## 💻 Usage Examples

### Basic File Operations
```php
use App\Classes\MediaManager;

// Store user avatar with validation
$path = MediaManager::storeAvatar($uploadedFile);

// Store course content for specific course
$path = MediaManager::storeCourseContent($videoFile, $courseId, 'videos');

// Get URLs for different media types
$avatarUrl = MediaManager::avatar('user123.jpg', 'thumbnails');
$courseUrl = MediaManager::courseContent('course-1', 'lesson1.mp4', 'videos');
$logoUrl = MediaManager::assetLogo('company-logo.svg', 'primary');
```

### Advanced Usage
```php
// Store with custom validation
$path = MediaManager::store('assets', 'images', $file, 'custom-name.jpg');

// Check configuration
$maxSize = MediaManager::config('categories.user.subdirectories.avatars.max_size');
$allowedTypes = MediaManager::config('categories.content.subdirectories.videos.allowed_types');

// Cleanup old files
MediaManager::cleanup();

// Ensure directory structure exists
MediaManager::ensureDirectoryStructure();
```

## 🔧 Configuration Highlights

### File Validation
- **Size limits**: Per-category maximum file sizes
- **Type validation**: Allowed file extensions and MIME types
- **Security**: Forbidden extensions and virus scanning options

### URL Generation
- **CDN support**: Optional CDN integration
- **Signed URLs**: Secure file access with expiration
- **Dynamic URLs**: Support for temporary and permanent links

### Processing Options
- **Auto-resize**: Automatic image resizing
- **Thumbnail generation**: Multiple thumbnail sizes
- **Format conversion**: WebP conversion for optimization

### Security Features
- **Upload limits**: Per-user daily upload limits
- **Storage quotas**: Maximum storage per user
- **File scanning**: Optional virus scanning integration

## 🚀 Artisan Commands

### Initialize Media Structure
```bash
php artisan media:init
```
Creates all configured directories and subdirectories.

### Clean Up Old Files
```bash
php artisan media:cleanup
```
Removes temporary and cached files based on retention settings.

## 🎛️ Environment Configuration

Add these to your `.env` file:

```env
# Media Configuration
MEDIA_DISK=media
MEDIA_CDN_ENABLED=false
MEDIA_CDN_DOMAIN=https://cdn.example.com
MEDIA_SIGNED_URLS=false
MEDIA_AUTO_RESIZE=true
MEDIA_WEBP_CONVERSION=true
MEDIA_VIRUS_SCAN=false
MEDIA_CLEANUP_ENABLED=true
MEDIA_LOGGING_ENABLED=true
MEDIA_LOG_CHANNEL=media
```

## 📊 Directory Structure Overview

### Assets Category
- **Purpose**: Static theme elements
- **Examples**: Backgrounds, icons, logos, vectors
- **Access**: `MediaManager::assetImage()`, `MediaManager::assetIcon()`

### Content Category  
- **Purpose**: Educational materials
- **Examples**: Course videos, documents, tutorials
- **Access**: `MediaManager::courseContent()`, `MediaManager::document()`

### User Category
- **Purpose**: User-generated content
- **Examples**: Avatars, uploads, certificates
- **Access**: `MediaManager::avatar()`, `MediaManager::userUpload()`

### System Category
- **Purpose**: System-generated files
- **Examples**: Cache, temporary files, backups
- **Access**: `MediaManager::cached()`, `MediaManager::temp()`

## 🛡️ Security Considerations

1. **File Type Validation**: Only allowed extensions can be uploaded
2. **Size Limits**: Configurable per-category size restrictions  
3. **Forbidden Extensions**: Security-sensitive file types are blocked
4. **User Quotas**: Prevent abuse with upload and storage limits
5. **Clean URLs**: Optional signed URLs for secure access

## 🔄 Integration with Laravel Features

- **Storage Facade**: Full compatibility with Laravel's Storage system
- **File Validation**: Integrates with Laravel's file validation rules
- **URL Generation**: Uses Laravel's URL generation with optional signing
- **Configuration**: Follows Laravel's configuration file patterns
- **Artisan Commands**: Custom commands for media management tasks

This system provides a solid foundation for scalable media management while maintaining security, performance, and developer experience.
