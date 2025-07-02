<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <h4>Open Lesson</h4>
            @foreach ($content['lessons'] as $lesson)
                <ul class="list-group">
                    <li class="list-group-item">
                        <strong>{{ $lesson['title'] }}</strong>
                        
                        @if(in_array($lesson['id'], $content['StudentPreviousLessons']))
                            <span class="badge badge-success float-right">
                               <i class="fa fa-check"></i> Completed
                            </span>
                        @else
                        <span class="badge badge-secondary float-right">
                            <i class="fa fa-ban"></i> Incomplete
                        </span>
                        @endif
                    </li>
                </ul>
            @endforeach
        </div>
        <div class="col-md-6">
            <h4>Completed Lesson</h4>
           
        </div>
    </div>
</div>
