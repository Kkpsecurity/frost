@props([
    'path' => '',
    'disk' => 'media',
    'fallback' => null,
    'alt' => '',
    'class' => '',
    'width' => null,
    'height' => null
])

@php
    use App\Helpers\MediaHelper;
    $imageUrl = MediaHelper::url($path, $disk, $fallback);
@endphp

<img src="{{ $imageUrl }}"
     alt="{{ $alt }}"
     @if($class) class="{{ $class }}" @endif
     @if($width) width="{{ $width }}" @endif
     @if($height) height="{{ $height }}" @endif
     {{ $attributes }}
>
