<div class="row mb-3">
    <div class="col-lg-2-4 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $content['stats']['total'] }}</h3>
                <p>Total Dates</p>
            </div>
            <div class="icon">
                <i class="fas fa-calendar"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-2-4 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $content['stats']['active'] }}</h3>
                <p>Active</p>
            </div>
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-2-4 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $content['stats']['upcoming'] }}</h3>
                <p>Upcoming</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-2-4 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $content['stats']['this_week'] }}</h3>
                <p>This Week</p>
            </div>
            <div class="icon">
                <i class="fas fa-calendar-week"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-2-4 col-6">
        <div class="small-box bg-secondary">
            <div class="inner">
                <h3>{{ $content['stats']['this_month'] }}</h3>
                <p>This Month</p>
            </div>
            <div class="icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
        </div>
    </div>
</div>
