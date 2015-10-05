@extends('layouts.master')
@section('title', 'Account: Register')
@section('page_title', 'Account: Register')
@section('content')
<form method="POST" action="{{ route('user::signup_post')}}" class="form-horizontal">
    {!! csrf_field() !!}

    <!-- First and Last name -->
    <div class="form-group">
        <label for="email" class="col-sm-offset-2 col-sm-3 control-label">First name</label>
        <div class="col-sm-3">
            <input type="text" name="first_name" id="first_name" placeholder="First name" class="form-control" value="{{ old('first_name') }}">
        </div>
    </div>
    <div class="form-group">
        <label for="last_name" class="col-sm-offset-2 col-sm-3 control-label">Last name</label>
        <div class="col-sm-3">
            <input type="text" name="last_name" id="last_name" placeholder="Last name" class="form-control" value="{{ old('first_name') }}">
        </div>
    </div>

    <!-- Email -->
    <div class="form-group">
        <label for="email" class="col-sm-offset-2 col-sm-3 control-label">Email</label>
        <div class="col-sm-3">
            <input type="email" name="email" id="email" placeholder="Email" class="form-control" value="{{ old('email') }}">
        </div>
    </div>

    <!-- Password -->
    <div class="form-group">
        <label for="password" class="col-sm-offset-2 col-sm-3 control-label">Password</label>
        <div class="col-sm-3">
            <input type="password" name="password" id="password" placeholder="Password" class="form-control">
        </div>
    </div>

    <!-- Password confirmation -->
    <div class="form-group">
        <label for="password_confirmation" class="col-sm-offset-2 col-sm-3 control-label">Confirm Password</label>
        <div class="col-sm-3">
            <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Password" class="form-control">
        </div>
    </div>

    <!-- Submit -->
    <div class="form-group">
        <div class="col-sm-offset-5 col-sm-3">
            <button type="submit" class="btn btn-default">Register</button>
        </div>
    </div>
</form>
@endsection
