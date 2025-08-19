@if ($message = Session::get('success'))
    <div class="alert alert-success">{{ $message }}</div>
@endif

@if ($message = Session::get('error'))
    <div class="alert alert-danger">{{ $message }}</div>
@endif

@if ($message = Session::get('warning'))
    <div class="alert alert-warning">{{ $message }}</div>
@endif

@if ($message = Session::get('info'))
    <div class="alert alert-info">{{ $message }}</div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
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
        <div class="alert alert-{{ $message['level'] }}">
            {!! $message['message'] !!}
        </div>
    @endif
@endforeach
