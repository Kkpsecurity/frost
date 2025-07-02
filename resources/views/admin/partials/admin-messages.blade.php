@if ($message = Session::get('success'))
    <div class="alert alert-success" role="alert">
        {{ $message }}
    </div>
@endif

@if ($message = Session::get('error'))
    <div class="alert alert-danger" role="alert">
        {{ $message }}
    </div>
@endif

@if ($message = Session::get('warning'))
    <div class="alert alert-warning" role="alert">
        {{ $message }}
    </div>
@endif

@if ($message = Session::get('info'))
    <div class="alert alert-info" role="alert">
        {{ $message }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        {{ $errors->first() }}
    </div>
@endif

@foreach (session('flash_notification', collect())->toArray() as $message)
    @if ($message['overlay'])
        @include('flash::modal', [
            'modalClass' => 'flash-modal',
            'title' => $message['title'],
            'body' => $message['message'],
        ])
    @else
        <div class="alert alert-{{ $message['level'] }}" role="alert">
            {{ $message['message'] }}
        </div>
    @endif
@endforeach

{{ session()->forget('flash_notification') }}
