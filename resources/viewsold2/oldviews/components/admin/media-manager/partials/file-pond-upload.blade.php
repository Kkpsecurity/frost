{{-- FilePond Upload Component --}}
<div class="form-group">
    @if($label)
        <label for="{{ $id }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    <input
        type="file"
        id="{{ $id }}"
        name="{{ $name }}"
        class="filepond-input @error($name) is-invalid @enderror"
        data-filepond="{{ $type }}"
        @if($multiple) multiple @endif
        @if($required) required @endif
        @if($value) value="{{ $value }}" @endif
    >

    @if($help)
        <small class="form-text text-muted">
            <i class="fas fa-info-circle"></i>
            {{ $help }}
        </small>
    @endif

    @error($name)
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize FilePond for this specific input
    const element = document.getElementById('{{ $id }}');
    if (element && typeof window.FilePond !== 'undefined') {
        const options = @json(array_merge($getDefaultOptions(), $options));

        // Create FilePond instance
        const pond = window.FilePond.create(element, options);

        // Store pond instance for later access
        element.filePondInstance = pond;

        // Custom event handling
        pond.on('processfile', (error, file) => {
            if (!error) {
                console.log('FilePond: File processed successfully', file.filename);

                // Trigger custom event
                element.dispatchEvent(new CustomEvent('filepond:processed', {
                    detail: { file, pond }
                }));
            }
        });

        pond.on('removefile', (error, file) => {
            if (!error) {
                console.log('FilePond: File removed', file.filename);

                // Trigger custom event
                element.dispatchEvent(new CustomEvent('filepond:removed', {
                    detail: { file, pond }
                }));
            }
        });

        pond.on('addfile', (error, file) => {
            if (!error) {
                console.log('FilePond: File added', file.filename);

                // Trigger custom event
                element.dispatchEvent(new CustomEvent('filepond:added', {
                    detail: { file, pond }
                }));
            }
        });

        // Error handling
        pond.on('error', (error) => {
            console.error('FilePond Error:', error);

            // Show user-friendly error message
            if (error.type === 'error' && error.body) {
                // Try to show the error using AdminLTE or Bootstrap toast
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Upload Error',
                        text: error.body,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 5000
                    });
                } else {
                    alert('Upload Error: ' + error.body);
                }
            }
        });
    }
});
</script>
@endpush

@once
@push('styles')
    @vite('resources/css/filepond.css')
@endpush

@push('scripts')
    @vite('resources/js/filepond.js')
@endpush
@endonce
