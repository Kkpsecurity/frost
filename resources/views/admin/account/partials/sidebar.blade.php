<div class="col-md-3">

    <div class="card card-primary card-outline">
        <div class="card-body box-profile">
            <div class="text-center">
                <img class="profile-user-img img-fluid img-circle"
                     src="{{ Auth()->user()->getAvatar('thumb') }}" alt="{{ Auth()->user()->fname }} {{ Auth()->user()->lname }}">
            </div>
            <h3 class="profile-username text-center">{{ Auth()->user()->fname }} {{ Auth()->user()->lname }}</h3>
            <p class="text-muted text-center"><i class="fa fa-envelope"></i> {{ Auth()->user()->email }}</p>
            <ul class="list-group list-group-unbordered mb-3">
                {{--<li class="list-group-item">
                    <b>Total Courses</b> <a class="float-right">0</a>
                </li>
                <li class="list-group-item">
                    <b>Following</b> <a class="float-right">543</a>
                </li>
                <li class="list-group-item">
                    <b>Friends</b> <a class="float-right">13,287</a>
                </li>--}}
            </ul>
            {{--<a href="#" class="btn btn-primary btn-block"><b>Follow</b></a>--}}
        </div>

    </div>


   {{-- <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ __('About Me') }}</h3>
        </div>

        <div class="card-body">
            <strong><i class="fas fa-book mr-1"></i> Education</strong>
            <p class="text-muted">
                B.S. in Computer Science from the University of Tennessee at Knoxville
            </p>
            <hr>
            <strong><i class="fas fa-map-marker-alt mr-1"></i> Location</strong>
            <p class="text-muted">{{ Auth()->user()->city }}, {{ Auth()->user()->state }}</p>
            <hr>
            <strong><i class="fas fa-pencil-alt mr-1"></i> Skills</strong>
            <p class="text-muted">
                <span class="tag tag-danger">UI Design</span>
                <span class="tag tag-success">Coding</span>
                <span class="tag tag-info">Javascript</span>
                <span class="tag tag-warning">PHP</span>
                <span class="tag tag-primary">Node.js</span>
            </p>
            <hr>
            <strong><i class="far fa-file-alt mr-1"></i> Notes</strong>
            <p class="text-muted">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam fermentum enim neque.</p>
        </div>

    </div>--}}

</div>
