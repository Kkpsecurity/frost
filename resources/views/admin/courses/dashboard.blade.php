@extends('layouts.admin')

@section('content')
     <div class="row">
          <div class="col-lg-12">
               <div class="alert alert-info">
                    <strong>Info!</strong> Daily Course are generated automatically this list is all course that have been generated
               </div>
               <div id="container-fluid">
                    <div class="row">
                         <div class="col-lg-12">
                              <div class="card-body">
                                   <div class="row">
                                       <div class="col-12">
                                           {!! $content['SwiftCrud'] !!}                                   >
                                       </div>
                                   </div>
                               </div>
                         </div>
                    </div>
               </div>
          </div>
     </div>
@stop