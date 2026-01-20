@extends('adminlte::page')

@section('title', 'Create Order')

@section('content_header')
    <h1>Create Order</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="alert alert-info">
                <i class="icon fas fa-info-circle"></i>
                Order creation is under development.
            </div>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Orders
            </a>
        </div>
    </div>
</div>
@stop
