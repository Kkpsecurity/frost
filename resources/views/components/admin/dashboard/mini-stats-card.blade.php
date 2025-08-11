@props([
    'title',
    'count',
    'icon',
    'color' => 'primary',
    'link' => '#',
    'linkText' => 'View',
    'colSize' => 'col-lg-2 col-6'
])

<div class="{{ $colSize }}">
    <div class="small-box bg-{{ $color }}">
        <div class="inner">
            <h3>{{ $count }}</h3>
            <p>{{ $title }}</p>
        </div>
        <div class="icon">
            <i class="{{ $icon }}"></i>
        </div>
        <a href="{{ $link }}" class="small-box-footer">
            {{ $linkText }} <i class="fas fa-arrow-circle-right"></i>
        </a>
    </div>
</div>
