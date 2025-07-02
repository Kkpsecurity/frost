<div class="col-md-4 col-sm-4 col-12">
    <div
        class="card contact-icon-card d-flex flex-column align-items-center justify-content-center text-center h-100">
        <div class="single-icon">
            <i class="{{ $card['icon'] }}"></i>
            <ul class="list-group w-100">
                <li class="list-group-item">
                    <h5>{{ $card['title'] }}:</h5>

                    @if ($card['title'] == 'Email')
                        @if (is_string($card['content']))
                            @foreach (explode(',', $card['content']) as $email)
                                <a href="mailto:{{ trim($email) }}">{{ trim($email) }}</a><br>
                            @endforeach
                        @endif
                    @else
                        @if (is_string($card['content']))
                            @foreach (explode(',', $card['content']) as $contact)
                                @nl2br(trim($contact))<br>
                            @endforeach
                        @endif
                    @endif
                    <span>{{ $card['subtitle'] }}</span>
                </li>
            </ul>
        </div>
    </div>
</div>