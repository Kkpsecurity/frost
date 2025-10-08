 <div class="border-0 shadow-lg bg-default">
     <div class="card-body login-form">
         <div class="account-profile d-flex flex-column align-items-center mt-2">
             <div class="avatar">
                 <img src="{{ Auth()->user()->getAvatar('thumb') }}" alt="Avatar">
             </div>
             <div class="profile-details text-white">
                 <h4 class="text-white">{{ Auth::user()->fname }} {{ Auth::user()->lname }}</h4>
                 <p class="text-white-50">{{ Auth::user()->email }}</p>
             </div>
         </div>
     </div>
 </div>
