<!-- Date range filter -->
<form action="{{ route($options['parent_route']) }}" method="POST" id="daterange-form">
    @csrf
    <div class="search-filter-container">
        <div class="search-filter input-group">
            <div class="input-group-prepend">
                <span class="input-group-text p-3"><i class="far fa-calendar-alt"></i></span>
            </div>
            <input type="text" class="form-control search-filter__input float-right" id="daterange" name="daterange"
                placeholder="Select a date range">
            <div class="input-group-append">
                <button type="submit" class="btn btn-default search-filter__button"><i
                        class="fas fa-search"></i></button>
                @if (Request::get('daterange'))
                    <a href="{{ route($options['parent_route']) }}" class="btn btn-default search-filter__button">
                        <i class="fa fa-times"></i>
                    </a>
                @endif
            </div>
        </div>
    </div>
</form>

<script>
    document.getElementById('daterange').addEventListener('input', function() {
        if (!this.value) {
            document.getElementById('search').value = '';
            document.getElementById('daterange-form').submit();
        }
    });
</script>
