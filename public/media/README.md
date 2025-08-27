# Frost Media Directory Structure

The `media` directory serves as the root for all assets in the Frost application, whether using local public disk or S3 storage.

## Directory Structure

```
media/
├── assets/                     # Static application assets
│   ├── images/                # General application images
│   ├── icons/                 # Icon files and graphics
│   ├── logos/                 # Company and brand logos
│   └── backgrounds/           # Background images and patterns
├── content/                   # Dynamic content assets
│   ├── courses/              # Course-related media (thumbnails, materials)
│   ├── documents/            # PDF documents, manuals, guides
│   └── videos/               # Video content (course videos, tutorials)
├── user/                      # User-generated content
│   ├── avatars/              # User profile pictures
│   ├── uploads/              # User file uploads
│   └── certificates/         # Generated certificates
└── system/                    # System-generated files
    ├── cache/                # Cached media files
    └── temp/                 # Temporary files
```

## Usage Guidelines

### Assets Directory
- **images/**: General application images, UI graphics
- **icons/**: Course icons, UI icons, favicons
- **logos/**: Company logos, partner logos, certification badges
- **backgrounds/**: Hero backgrounds, section backgrounds

### Content Directory
- **courses/**: Course thumbnails, preview images, course materials
- **documents/**: Handbooks, guides, forms, legal documents
- **videos/**: Course videos, promotional videos, tutorials

### User Directory
- **avatars/**: User profile pictures (auto-generated or uploaded)
- **uploads/**: Files uploaded by users during courses
- **certificates/**: Generated completion certificates

### System Directory
- **cache/**: Processed/optimized versions of media files
- **temp/**: Temporary files during upload/processing

## Configuration

The media directory is configured in `config/filesystems.php`:

- **Local Environment**: `storage/app/public/media/`
- **Production S3**: `s3://bucket-name/media/`
- **Access URL**: `/media/` (via symbolic link)

## File Naming Conventions

- Use lowercase with hyphens: `course-thumbnail.jpg`
- Include dimensions for images: `hero-bg-1920x1080.jpg`
- Version files when updated: `logo-v2.png`
- Use descriptive names: `class-d-security-icon.png`

## Media Management

This structure is designed to work with:
- Laravel's Storage facade
- Future Media Manager implementation
- CDN integration
- Automatic optimization pipelines

## Environment Setup

To set up the symbolic link for media access:

```bash
php artisan storage:link
```

This creates a symbolic link from `public/media` to `storage/app/public/media`.
