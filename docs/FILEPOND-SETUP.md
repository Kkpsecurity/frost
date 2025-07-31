# FilePond + Laravel Media Library + AWS S3 Integration

This project includes a complete file upload system using FilePond with optional AWS S3 storage support.

## Features

- **FilePond Integration**: Modern file upload with drag & drop
- **Multiple File Types**: Images, documents, videos, archives
- **Dark Theme**: Matches AdminLTE dark theme
- **AWS S3 Support**: Optional cloud storage
- **Laravel Media Library**: Backend file management
- **Responsive Design**: Mobile-friendly interface

## Setup

### 1. Environment Configuration

Add these variables to your `.env` file:

```bash
# FilePond S3 Settings (optional)
FILEPOND_USE_S3=true              # Set to true to enable S3 storage
FILEPOND_S3_DISK=s3               # Laravel disk name for S3
FILEPOND_S3_PATH=uploads          # Base path in S3 bucket

# AWS S3 Configuration (required if using S3)
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket-name
AWS_URL=                          # Optional: Custom S3 endpoint URL
AWS_ENDPOINT=                     # Optional: For S3-compatible services
AWS_USE_PATH_STYLE_ENDPOINT=false # Set to true for MinIO/custom endpoints
```

### 2. File Storage Disks

The system automatically detects storage configuration:

- **Local Storage**: Files stored in `storage/app/public/uploads/`
- **S3 Storage**: Files stored in your S3 bucket under the configured path

### 3. Usage Examples

#### Basic File Upload

```blade
<x-admin.file-pond-upload 
    id="basic-upload"
    name="files[]"
    accept="image/*"
    :multiple="true"
/>
```

#### Document Upload with Validation

```blade
<x-admin.file-pond-upload 
    id="document-upload"
    name="documents[]"
    accept=".pdf,.doc,.docx"
    :multiple="false"
    max-file-size="10MB"
/>
```

#### Image Upload with Preview

```blade
<x-admin.file-pond-upload 
    id="image-upload"
    name="images[]"
    accept="image/*"
    :multiple="true"
    image-preview="true"
/>
```

### 4. JavaScript Integration

```javascript
// Initialize FilePond with custom options
const pond = createFilePond('#my-upload', {
    acceptedFileTypes: ['image/*'],
    maxFileSize: '5MB',
    allowMultiple: true,
    onaddfile: (error, file) => {
        if (error) {
            console.error('File add error:', error);
            return;
        }
        console.log('File added:', file.filename);
    },
    onprocessfile: (error, file) => {
        if (error) {
            console.error('Upload error:', error);
            return;
        }
        console.log('Upload complete:', file.filename);
    }
});
```

### 5. Backend Integration

#### Simple Upload Handling

```php
// In your controller
public function store(Request $request)
{
    $request->validate([
        'uploads' => 'required|array',
        'uploads.*' => 'string'
    ]);

    // Finalize uploads
    $response = app(MediaController::class)->finalize($request);
    
    if ($response->getStatusCode() === 200) {
        $data = json_decode($response->getContent(), true);
        $files = $data['files'];
        
        // Process your uploaded files
        foreach ($files as $file) {
            // File is now permanently stored
            // Access via $file['url'], $file['path'], etc.
        }
    }
}
```

#### Attach Files to Models

```php
// Attach uploads to a model
$finalizeRequest = new Request([
    'uploads' => $request->input('uploads'),
    'model_type' => User::class,
    'model_id' => auth()->id(),
    'collection' => 'profile_images'
]);

$response = app(MediaController::class)->finalize($finalizeRequest);
```

## File Organization

The system automatically organizes files by type:

- **Images**: `uploads/images/`
- **Videos**: `uploads/videos/`
- **Documents**: `uploads/documents/`
- **Archives**: `uploads/archives/`
- **Other**: `uploads/files/`

## Security Features

- File type validation
- Size limits (configurable)
- Temporary upload cleanup
- Secure file naming
- CSRF protection

## Demo

Visit `/admin/admin-center/media` to see working examples of all upload types, or access it via the admin menu:
**Admin Center → Media Manager**

## Troubleshooting

### S3 Upload Issues

1. Verify AWS credentials are correct
2. Check bucket permissions (public read access required for public files)
3. Ensure bucket CORS is configured for your domain
4. Verify `FILEPOND_USE_S3=true` in `.env`

### Local Upload Issues

1. Ensure `storage/app/public` is writable
2. Run `php artisan storage:link` to create symbolic link
3. Check file permissions on storage directory

### Browser Console Errors

- Open browser dev tools to see FilePond error messages
- Check network tab for HTTP error responses
- Verify CSRF token is being sent with requests
