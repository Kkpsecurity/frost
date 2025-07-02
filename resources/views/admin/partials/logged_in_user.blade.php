 <div class="user-panel mt-3 pb-3 mb-3 d-flex">
     <div class="image">
          <img src="{{ auth() ->user()->getAvatar('thumb') }}" class="img-circle elevation-2" alt="{{ auth() ->user()->fullname() }}">
     </div>
     <div class="info" style="width:100%; padding: 0; ">
          <a href="{{ route('admin.account.dashboard') }}" class="" style="padding: 15px;margin: 0; width:75%">{{ auth()->user()->fullname()  }}</a>
          <a  href="{{ route('logout') }}" class="float-right" style="margin: 0; color: orangered"
          onclick="event.preventDefault();document.getElementById('logout-form').submit();">
          <i class="fas fa-power-off t"></i>
          </a>
          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
     </div>
</div>