@extends('layouts.admin')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/swiftcrud.css') }}">
@stop

@section('content')
    <section class="content">
        <div class="container-fluid p-4">
            <div class="row">
                <div class="col-12">
                    @include('admin.partials.admin-messages')
                    <div id="message-console"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="container-fluid">
                        <ul class="nav nav-tabs bg-light" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="orders-tab" data-toggle="tab" href="#orders" role="tab"
                                    aria-controls="orders" aria-selected="true">Orders</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="auths-tab" data-toggle="tab" href="#auths" role="tab"
                                    aria-controls="auths" aria-selected="false">Auths</a>
                            </li>
                        </ul>
                        <div class="tab-content bg-gray" id="orderTabs" style="min-height: 600px: height: auto">
                            <div class="tab-pane fade show active" id="orders" role="tabpanel"
                                aria-labelledby="orders-tab">
                                @include('admin.orders.partials.orders', ['OrdersTable' => $content['OrdersTable']])
                            </div>
                            <div class="tab-pane fade" id="auths" role="tabpanel" aria-labelledby="auths-tab">
                                @include('admin.orders.partials.auths')
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
        'route' => ['admin.students', 'delete'],
        'method' => 'DELETE',
        'id' => 'form-delete-record',
    ]);
    
    $deleteRecord .= __('Are you sure you want to delete this item?');
    $deleteRecord .= Form::hidden('record_id', null, ['id' => 'record_id']);
    $deleteRecord .= Form::close();
    
@endphp

@section('modals')
    {!! App\Support\LTEBootstrap::modal([
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
                'id' => '',
                'class' => 'btn-default',
                'dismiss' => true,
            ],
        ],
        'footer' => true,
        'modal_type' => 'danger', // or 'success'
    ]) !!}
@endsection

@section('scripts')
    <script>
        $.noConflict();
    </script>

    <script src="{{ asset('assets/admin/js/swiftcrud.js') }}"></script>
@endsection
