@extends('layouts.admin')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/swiftcrud.css') }}">
@stop

@section('content')
<section class="content">
     <div class="container-fluid">
          <div class="row">
               <div class="col-12">
                    @include('admin.partials.admin-messages')
                    <div id="message-console"></div>
               </div>
          </div>
          <div class="row">
               <div class="col-12">
                    <div class="card">
                         <div class="card-header">
                              <div class="row">
                                   <div class="col-lg-6">
                                        <h3 class="card-title"><i class="fa fa-users"></i> {{ __('Student Management') }}</h3>
                                   </div>
                                   <div class="col-lg-6">
                                        <div class="float-right">
                                             @if(request()->segment(4))
                                                  <a href="{{ route('admin.students') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> {{ __('Back') }}</a>
                                             @else
                                                  <a href="{{ route('admin.students', ['create']) }}" class="btn btn-primary"><i class="fa fa-plus"></i> {{ __('Add Student') }}</a>
                                             @endif
                                        </div>
                                   </div>
                              </div>
                         </div>
                         <div class="card-body">
                              <div class="row">
                                   <div class="col-12">
                                        {!! $content['SwiftCrud'] !!}
                                   </div>                                  
                              </div>
                         </div>
                    </div>
               </div>
          </div>
     </div>
</section>
@stop

@php 
   $deleteRecord = Form::open([
        'route' => [
            'admin.students', 'delete'
        ], 
        'method' => 'DELETE', 
        'id' => 'form-delete-record'
    ]);

    $deleteRecord .= __('Are you sure you want to delete this item?');
    $deleteRecord .= Form::hidden('record_id', null, ['id' => 'record_id']);
    $deleteRecord .= Form::close();

@endphp

@section('modals')    
{!!
    App\Support\LTEBootstrap::modal([
        'modal_id' => 'delete-record-modal',
        'modal_title' => __('Delete Item'),
        'modal_content' => $deleteRecord,
        'footer_buttons' => [
            'button' => [
                'label' => __('Delete'),
                'class' => 'btn-danger',
                'id' => 'delete-record-btn',
                'dismiss' => false,                
            ],
            'button2' => [
                'label' => __('Cancel'),
                'id'=> "",
                'class' => 'btn-default',
                'dismiss' => true,
            ],
        ],
        'footer' => true,
        'modal_type' => 'danger', // or 'success'
    ]);

!!}
@endsection

@section('scripts')
    <script src="{{ asset('assets/admin/js/swiftcrud.js') }}"></script>
@endsection




    