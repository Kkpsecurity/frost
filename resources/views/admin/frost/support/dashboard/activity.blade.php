<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><i class="fa fa-user mr-2"></i> User ID:</span> {{ @$activity['user_id'] }}
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><i class="fa fa-calendar mr-2"></i> Date Purchaed:</span> {{ @$activity['created_at'] }}
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><i class="fa fa-calendar mr-2"></i> Started Course:</span> {{ @$activity['created_at'] }}
                </li>
                
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><i class="fa fa-globe mr-2"></i> Browser:</span>
                    {{ @substr($activity['browser'], 0, 30) ?? 'No Browser Recorded' }}
                </li>                
            </ul>
        </div>
    </div>
</div>
