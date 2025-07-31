@extends('layouts.admin')

@section('content')
    <section class="container-fluid p-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Create New Admin User</h1>
            <a href="{{ route('admin.admin-center.adminusers') }}" class="btn btn-outline-secondary">Back to Users</a>
        </div>
        <div class="row">
            <div class="col-md-6">
                {!! Form::open(['route' => 'admin.admin-center.adminusers.store', 'id' => 'create-admin-user-form']) !!}

                <div class="form-group">
                    {!! Form::label('name', 'Name') !!}
                    {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('email', 'Email') !!}
                    {!! Form::email('email', null, ['class' => 'form-control', 'required']) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('password', 'Password') !!}
                    {!! Form::password('password', ['class' => 'form-control', 'required']) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('password_confirmation', 'Confirm Password') !!}
                    {!! Form::password('password_confirmation', ['class' => 'form-control', 'required']) !!}
                </div>

                <button type="submit" class="btn btn-primary">Create</button>
                {!! Form::close() !!}
            </div>
        </div>
    </section>

@stop
