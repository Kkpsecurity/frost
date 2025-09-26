@props([
    'title',
    'icon' => null,
    'subtitle' => null,
    'iconColor' => 'primary'
])

<div class="row mt-4">
    <div class="col-12">
        <h4 class="mb-3">
            @if($icon)
                <i class="{{ $icon }} text-{{ $iconColor }}"></i>
            @endif
            {{ $title }}
            @if($subtitle)
                <small class="text-muted">{{ $subtitle }}</small>
            @endif
        </h4>
    </div>
</div>
