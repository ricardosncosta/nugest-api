@extends('layouts.master')
@section('title', 'Signin')
@section('page_title', 'Signin')
@section('content')
<form method="POST" action="{{ route('signin_post')}}" class="form-horizontal">
    {!! csrf_field() !!}

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

    <!-- Remember Me -->
    <div class="form-group">
        <div class="col-sm-offset-5 col-sm-3">
            <div class="checkbox">
                <label for="remember">
                    <input type="checkbox" name="remember" id="remember"> Remember Me
                </label>
            </div>
        </div>
    </div>

    <!-- Submit -->
    <div class="form-group">
        <div class="col-sm-offset-5 col-sm-3">
            <button type="submit" class="btn btn-default">Signin</button>
            Or <a href="{{ route('user::signup_get') }}">Signup</a> for an account.
        </div>
    </div>
</form>
@endsection
