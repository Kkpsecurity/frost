<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Media Management Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration file defines the media asset organization structure
    | for the FROST application. It provides centralized mapping for all
    | media types, storage locations, and access patterns.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Default Storage Disk
    |--------------------------------------------------------------------------
    |
    | The default disk that should be used for media storage operations.
    | This should correspond to a disk configured in filesystems.php
    |
    */

    'default_disk' => env('MEDIA_DISK', 'media'),

    /*
    |--------------------------------------------------------------------------
    | Media Root Directory
    |--------------------------------------------------------------------------
    |
    | The root directory within the storage disk where all media files
    | are organized. This provides a consistent base path for all media.
    |
    */

    'root_directory' => 'media',

    /*
    |--------------------------------------------------------------------------
    | Asset Categories Configuration
    |--------------------------------------------------------------------------
    |
    | Define the directory structure and configuration for each type of
    | media asset in the system. Each category maps to specific directories
    | and includes validation rules and processing options.
    |
    */

    'categories' => [

        /*
        |----------------------------------------------------------------------
        | Static Theme Assets
        |----------------------------------------------------------------------
        |
        | Assets that are part of the theme/design system and rarely change.
        | These include backgrounds, icons, logos, and design elements.
        |
        */

        'assets' => [
            'directory' => 'assets',
            'description' => 'Static theme assets and design elements',
            'subdirectories' => [
                'images' => [
                    'path' => 'images',
                    'allowed_types' => ['jpg', 'jpeg', 'png', 'webp', 'svg'],
                    'max_size' => 10 * 1024 * 1024, // 10MB
                    'subdirs' => [
                        'backgrounds' => 'Background images for pages and sections',
                        'heroes' => 'Hero section images',
                        'placeholders' => 'Placeholder and default images',
                        'gallery' => 'Image galleries and showcases',
                    ]
                ],
                'icons' => [
                    'path' => 'icons',
                    'allowed_types' => ['svg', 'png', 'ico'],
                    'max_size' => 1 * 1024 * 1024, // 1MB
                    'subdirs' => [
                        'brands' => 'Brand and company icons',
                        'categories' => 'Category and classification icons',
                        'custom' => 'Custom application icons',
                    ]
                ],
                'logos' => [
                    'path' => 'logos',
                    'allowed_types' => ['svg', 'png', 'jpg'],
                    'max_size' => 5 * 1024 * 1024, // 5MB
                    'subdirs' => [
                        'primary' => 'Main application logos',
                        'variants' => 'Logo variations and alternatives',
                        'partners' => 'Partner and client logos',
                    ]
                ],
                'vectors' => [
                    'path' => 'vectors',
                    'allowed_types' => ['svg'],
                    'max_size' => 2 * 1024 * 1024, // 2MB
                    'description' => 'SVG illustrations and graphics',
                ]
            ]
        ],

        /*
        |----------------------------------------------------------------------
        | Educational Content
        |----------------------------------------------------------------------
        |
        | Content related to courses, lessons, and educational materials.
        | This includes videos, documents, and course-specific assets.
        |
        */

        'content' => [
            'directory' => 'content',
            'description' => 'Educational content and course materials',
            'subdirectories' => [
                'courses' => [
                    'path' => 'courses',
                    'dynamic_structure' => true, // Uses {course-id} subdirectories
                    'allowed_types' => ['mp4', 'webm', 'pdf', 'jpg', 'png', 'zip'],
                    'max_size' => 500 * 1024 * 1024, // 500MB for videos
                    'subdirs' => [
                        'videos' => 'Course video content',
                        'materials' => 'PDF documents and resources',
                        'thumbnails' => 'Course preview thumbnails',
                        'previews' => 'Preview content and samples',
                    ]
                ],
                'documents' => [
                    'path' => 'documents',
                    'allowed_types' => ['pdf', 'doc', 'docx', 'txt', 'md'],
                    'max_size' => 50 * 1024 * 1024, // 50MB
                    'subdirs' => [
                        'policies' => 'Legal documents and policies',
                        'guides' => 'User guides and documentation',
                        'templates' => 'Document templates and forms',
                    ]
                ],
                'videos' => [
                    'path' => 'videos',
                    'allowed_types' => ['mp4', 'webm', 'avi', 'mov'],
                    'max_size' => 1024 * 1024 * 1024, // 1GB
                    'subdirs' => [
                        'promotional' => 'Marketing and promotional videos',
                        'tutorials' => 'How-to and tutorial videos',
                        'archived' => 'Archived and legacy content',
                    ]
                ]
            ]
        ],

        /*
        |----------------------------------------------------------------------
        | User-Generated Content
        |----------------------------------------------------------------------
        |
        | Content uploaded and generated by users including avatars, 
        | assignments, certificates, and personal files.
        |
        */

        'user' => [
            'directory' => 'user',
            'description' => 'User-generated and personal content',
            'subdirectories' => [
                'avatars' => [
                    'path' => 'avatars',
                    'allowed_types' => ['jpg', 'jpeg', 'png', 'webp'],
                    'max_size' => 5 * 1024 * 1024, // 5MB
                    'subdirs' => [
                        'original' => 'Full-size avatar images',
                        'thumbnails' => 'Avatar thumbnails (auto-generated)',
                        'default' => 'Default avatar options',
                    ]
                ],
                'uploads' => [
                    'path' => 'uploads',
                    'dynamic_structure' => true, // Uses {user-id} subdirectories
                    'allowed_types' => ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'zip'],
                    'max_size' => 100 * 1024 * 1024, // 100MB
                    'subdirs' => [
                        'assignments' => 'Assignment submissions',
                        'projects' => 'Project files and portfolios',
                        'personal' => 'Personal documents and files',
                    ]
                ],
                'certificates' => [
                    'path' => 'certificates',
                    'allowed_types' => ['pdf', 'jpg', 'png'],
                    'max_size' => 10 * 1024 * 1024, // 10MB
                    'subdirs' => [
                        'generated' => 'Auto-generated certificates',
                        'templates' => 'Certificate templates',
                        'signed' => 'Digitally signed certificates',
                    ]
                ],
                'profiles' => [
                    'path' => 'profiles',
                    'allowed_types' => ['jpg', 'jpeg', 'png', 'pdf'],
                    'max_size' => 20 * 1024 * 1024, // 20MB
                    'subdirs' => [
                        'banners' => 'Profile banner images',
                        'portfolios' => 'Portfolio and showcase content',
                    ]
                ]
            ]
        ],

        /*
        |----------------------------------------------------------------------
        | System-Generated Content
        |----------------------------------------------------------------------
        |
        | Content generated by the system including cached files, temporary
        | uploads, optimized images, and system backups.
        |
        */

        'system' => [
            'directory' => 'system',
            'description' => 'System-generated and cached content',
            'subdirectories' => [
                'cache' => [
                    'path' => 'cache',
                    'allowed_types' => ['jpg', 'jpeg', 'png', 'webp'],
                    'max_size' => 50 * 1024 * 1024, // 50MB
                    'auto_cleanup' => true,
                    'cleanup_days' => 30,
                    'subdirs' => [
                        'images' => 'Cached image variations',
                        'thumbnails' => 'Generated thumbnails',
                        'optimized' => 'Optimized media files',
                    ]
                ],
                'temp' => [
                    'path' => 'temp',
                    'allowed_types' => ['*'], // Allow all types for temporary storage
                    'max_size' => 200 * 1024 * 1024, // 200MB
                    'auto_cleanup' => true,
                    'cleanup_hours' => 24,
                    'subdirs' => [
                        'uploads' => 'Temporary uploads in progress',
                        'processing' => 'Files being processed',
                        'exports' => 'Temporary export files',
                    ]
                ],
                'backups' => [
                    'path' => 'backups',
                    'allowed_types' => ['zip', 'sql', 'json'],
                    'max_size' => 1024 * 1024 * 1024, // 1GB
                    'subdirs' => [
                        'media' => 'Media file backups',
                        'databases' => 'Database backups',
                    ]
                ]
            ]
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | URL Generation Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for generating URLs to media assets. This includes
    | CDN settings, domain configuration, and URL signing options.
    |
    */

    'urls' => [
        'cdn_enabled' => env('MEDIA_CDN_ENABLED', false),
        'cdn_domain' => env('MEDIA_CDN_DOMAIN', null),
        'signed_urls' => env('MEDIA_SIGNED_URLS', false),
        'signed_url_expiry' => 60 * 24, // 24 hours in minutes
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Processing Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for automatic image processing including resize operations,
    | format conversions, and optimization settings.
    |
    */

    'processing' => [
        'auto_resize' => env('MEDIA_AUTO_RESIZE', true),
        'max_width' => 2048,
        'max_height' => 2048,
        'quality' => 85,
        'formats' => [
            'webp_conversion' => env('MEDIA_WEBP_CONVERSION', true),
            'thumbnail_format' => 'webp',
        ],
        'thumbnail_sizes' => [
            'small' => ['width' => 150, 'height' => 150],
            'medium' => ['width' => 300, 'height' => 300],
            'large' => ['width' => 600, 'height' => 600],
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | Security settings for media uploads and access including virus
    | scanning, access control, and file validation rules.
    |
    */

    'security' => [
        'virus_scan' => env('MEDIA_VIRUS_SCAN', false),
        'allowed_mime_types' => [
            // Images
            'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml',
            // Documents
            'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            // Videos
            'video/mp4', 'video/webm', 'video/avi', 'video/quicktime',
            // Archives
            'application/zip', 'application/x-rar-compressed',
        ],
        'forbidden_extensions' => [
            'exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'vbs', 'js', 'jar', 'php', 'asp'
        ],
        'max_uploads_per_user_per_day' => 100,
        'max_storage_per_user' => 1024 * 1024 * 1024, // 1GB
    ],

    /*
    |--------------------------------------------------------------------------
    | Cleanup Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for automatic cleanup of temporary files, cache, and
    | orphaned media assets.
    |
    */

    'cleanup' => [
        'enabled' => env('MEDIA_CLEANUP_ENABLED', true),
        'schedule' => 'daily', // daily, weekly, monthly
        'temp_file_retention' => 24, // hours
        'cache_retention' => 30, // days
        'orphaned_file_retention' => 7, // days
        'backup_retention' => 90, // days
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Configure logging for media operations including uploads, processing,
    | and access tracking.
    |
    */

    'logging' => [
        'enabled' => env('MEDIA_LOGGING_ENABLED', true),
        'channel' => env('MEDIA_LOG_CHANNEL', 'media'),
        'log_uploads' => true,
        'log_access' => false,
        'log_processing' => true,
        'log_errors' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Helper Methods Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the MediaManager helper class methods and their
    | default behaviors.
    |
    */

    'helpers' => [
        'generate_thumbnails' => true,
        'auto_optimize' => true,
        'preserve_originals' => true,
        'use_uuid_filenames' => true,
        'timestamp_filenames' => true,
    ],

];
