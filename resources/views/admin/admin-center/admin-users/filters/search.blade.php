<!-- Search filter -->
<form action="{{ route($options['parent_route']) }}" method="POST">
    @csrf
    <div class="form-group search-filter-container">
        <div class="input-group search-filter">
            <input type="text" class="form-control form-control-lg search-filter__input" id="search" name="search" placeholder="Type your keywords here" value="{{ Request::get('search') }}">
            <div class="input-group-append">
                <button type="submit" class="btn btn-lg search-filter__button btn-default"><i class="fa fa-search"></i></button>
                @if(Request::get('search'))
                    <div class="input-group-append">
                        <button type="button" id="clear-search" class="btn btn-lg search-filter__button btn-default"><i class="fa fa-times"></i></button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</form>

<script>
    document.getElementById('clear-search').addEventListener('click', function() {
        document.getElementById('search').value = '';
    });
</script>
