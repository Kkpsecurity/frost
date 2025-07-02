<div class="card">
    <div class="card-header">
         <div class="row">
              <div class="col-lg-6">
                   <h3 class="card-title"><i class="fa fa-users"></i> {{ __('Orders') }}</h3>
              </div>
              <div class="col-lg-6">
                   <div class="float-right">
                        @if(request()->segment(4))
                             <a href="{{ url('admin.orders') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> {{ __('Back') }}</a>
                        @else
                             <a href="{{ url('admin.orders', ['create']) }}" class="btn btn-primary"><i class="fa fa-plus"></i> {{ __('Add Student') }}</a>
                        @endif
                   </div>
              </div>
         </div>
    </div>
    <div class="card-body">
         <div class="row">
              <div class="col-12">
                   {!! $OrdersTable !!}
              </div>
         </div>
    </div>
</div>