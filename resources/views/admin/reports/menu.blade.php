<ul class="list-group">
    <li class="list-group-item">
        <a href="{{ route('admin.reports.dashboard', 'registration-chart') }}" data-target="registration-chart">
            Monthly trend of registrations - year month all
        </a>
    </li>
    <li class="list-group-item">
        <a href="{{ route('admin.reports.dashboard', 'registrations-vs-sales-chart') }}" data-target="registrations-vs-sales-chart">Monthly trend of registrations vs sales</a>
    </li>
    <li class="list-group-item">
        <a href="{{ route('admin.reports.dashboard', 'ratio-chart') }}" data-target="ratio-chart">
            Ratio of registrations to sales
        </a>
    </li>
</ul>