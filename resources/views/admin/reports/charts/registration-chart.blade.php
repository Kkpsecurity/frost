<div class="container mt-5 shadow">
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="timeViewTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="year-tab" data-toggle="tab" href="#year-tab-panel" role="tab"
                        aria-controls="year-tab-panel" aria-selected="true" data-view="year">Year</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="month-tab" data-toggle="tab" href="#month-tab-panel" role="tab"
                        aria-controls="month-tab-panel" aria-selected="false" data-view="month">Month</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <!-- New Title Section -->
            <h2 id="chartTitle"></h2>
            <div class="tab-content" id="timeViewTabContent">
                <div class="tab-pane fade show active" id="year-tab-panel" role="tabpanel" aria-labelledby="year-tab">
                    <canvas id="yearChart"></canvas>
                </div>
                <div class="tab-pane fade" id="month-tab-panel" role="tabpanel" aria-labelledby="month-tab">
                    <canvas id="monthChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
