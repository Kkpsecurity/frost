<div class="row mb-3">
    <div class="col-lg-6">
        <form action="{{ $url }}" method="get" class="form-inline">
            <div class="input-group">
                <input type="text" name="{{ $queryParam }}" class="form-control" placeholder="{{ $placeholder }}" aria-label="{{ $placeholder }}">
                <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
            </div>
        </form>
    </div>
</div>
