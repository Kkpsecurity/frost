<?php

namespace App\View\Components\Admin;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FilePondUpload extends Component
{
    public string $name;
    public string $id;
    public string $type;
    public bool $multiple;
    public ?string $acceptedFileTypes;
    public ?string $maxFileSize;
    public ?int $maxFiles;
    public bool $allowImagePreview;
    public bool $allowImageCrop;
    public ?string $label;
    public ?string $help;
    public bool $required;
    public ?string $value;
    public array $options;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $name,
        string $id = null,
        string $type = 'file',
        bool $multiple = false,
        string $acceptedFileTypes = null,
        string $maxFileSize = '10MB',
        int $maxFiles = null,
        bool $allowImagePreview = true,
        bool $allowImageCrop = false,
        string $label = null,
        string $help = null,
        bool $required = false,
        string $value = null,
        array $options = []
    ) {
        $this->name = $name;
        $this->id = $id ?? $name;
        $this->type = $type;
        $this->multiple = $multiple;
        $this->acceptedFileTypes = $acceptedFileTypes;
        $this->maxFileSize = $maxFileSize;
        $this->maxFiles = $maxFiles ?? ($multiple ? 10 : 1);
        $this->allowImagePreview = $allowImagePreview;
        $this->allowImageCrop = $allowImageCrop;
        $this->label = $label;
        $this->help = $help;
        $this->required = $required;
        $this->value = $value;
        $this->options = $options;
    }

    /**
     * Get default options based on upload type
     */
    public function getDefaultOptions(): array
    {
        $baseOptions = [
            'allowMultiple' => $this->multiple,
            'maxFiles' => $this->maxFiles,
            'maxFileSize' => $this->maxFileSize,
            'allowImagePreview' => $this->allowImagePreview,
            'allowImageCrop' => $this->allowImageCrop,
        ];

        switch ($this->type) {
            case 'image':
                return array_merge($baseOptions, [
                    'acceptedFileTypes' => $this->acceptedFileTypes ?? 'image/jpeg,image/png,image/gif,image/webp',
                    'allowImagePreview' => true,
                    'allowImageCrop' => $this->allowImageCrop,
                    'maxFileSize' => $this->maxFileSize ?? '5MB',
                    'labelIdle' => '<div class="filepond-drop-area">
                        <i class="fas fa-image fa-3x text-success mb-3"></i>
                        <div class="h5 mb-2">Drag & Drop your image or <span class="filepond-label-action text-success">Browse</span></div>
                        <div class="text-muted small">Supports: JPEG, PNG, GIF, WebP up to ' . ($this->maxFileSize ?? '5MB') . '</div>
                    </div>'
                ]);

            case 'document':
                return array_merge($baseOptions, [
                    'acceptedFileTypes' => $this->acceptedFileTypes ?? 'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,text/plain',
                    'allowImagePreview' => false,
                    'maxFileSize' => $this->maxFileSize ?? '25MB',
                    'labelIdle' => '<div class="filepond-drop-area">
                        <i class="fas fa-file-alt fa-3x text-info mb-3"></i>
                        <div class="h5 mb-2">Drag & Drop your document or <span class="filepond-label-action text-info">Browse</span></div>
                        <div class="text-muted small">Supports: PDF, DOC, DOCX, TXT up to ' . ($this->maxFileSize ?? '25MB') . '</div>
                    </div>'
                ]);

            case 'video':
                return array_merge($baseOptions, [
                    'acceptedFileTypes' => $this->acceptedFileTypes ?? 'video/mp4,video/webm,video/avi,video/mov',
                    'allowImagePreview' => false,
                    'maxFileSize' => $this->maxFileSize ?? '100MB',
                    'labelIdle' => '<div class="filepond-drop-area">
                        <i class="fas fa-video fa-3x text-warning mb-3"></i>
                        <div class="h5 mb-2">Drag & Drop your video or <span class="filepond-label-action text-warning">Browse</span></div>
                        <div class="text-muted small">Supports: MP4, WebM, AVI, MOV up to ' . ($this->maxFileSize ?? '100MB') . '</div>
                    </div>'
                ]);

            default:
                return array_merge($baseOptions, [
                    'acceptedFileTypes' => $this->acceptedFileTypes,
                    'labelIdle' => '<div class="filepond-drop-area">
                        <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-3"></i>
                        <div class="h5 mb-2">Drag & Drop your files or <span class="filepond-label-action text-primary">Browse</span></div>
                        <div class="text-muted small">Maximum file size: ' . ($this->maxFileSize ?? '10MB') . '</div>
                    </div>'
                ]);
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.file-pond-upload');
    }
}
