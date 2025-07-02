<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Course Schedule</h3>
    </div>

    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Day</th>
                    <th>Attendance</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($attendance as $day => $count)
                    @if ($day != 'Saturday' && $day != 'Sunday')
                        <tr>
                            <td>{{ $day }}</td>
                            <td class="text-right">
                                <span class="badge badge-{{ $count >= 1 ? 'success' : 'danger' }}"
                                    style="font-size: 14px;">{{ $count }}</span>
                            </td>
                        </tr>
                    @endif
                @endforeach

            </tbody>
        </table>
    </div>
</div>
