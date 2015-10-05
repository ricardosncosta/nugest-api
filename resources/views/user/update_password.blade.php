@extends('layouts.master')

@section('title', 'Account: Update password')
@section('page_title', 'Account: Update password')

@section('content')
<form method="POST" action="{{ route('user::update_password_post')}}" class="form-horizontal">
    {!! csrf_field() !!}

    <!-- Password -->
    <div class="form-group">
        <label for="password" class="col-sm-offset-2 col-sm-3 control-label">Current Password</label>
        <div class="col-sm-3">
            <input type="password" name="current_password" id="current_password" placeholder="Current Password" class="form-control">
        </div>
    </div>

	<p>&nbsp;</p>

    <!-- Email -->
    <div class="form-group">
        <label for="password" class="col-sm-offset-2 col-sm-3 control-label">New password</label>
        <div class="col-sm-3">
            <input type="password" name="password" id="password" placeholder="New password" class="form-control">
        </div>
    </div>

    <!-- Email confirmation -->
    <div class="form-group">
        <label for="password_confirmation" class="col-sm-offset-2 col-sm-3 control-label">New password confirmation</label>
        <div class="col-sm-3">
            <input type="password" name="password_confirmation" id="password_confirmation" placeholder="New password confirmation" class="form-control">
        </div>
    </div>

    <!-- Submit -->
    <div class="form-group">
        <div class="col-sm-offset-5 col-sm-3">
            <button type="submit" class="btn btn-default">Update password</button>
        </div>
    </div>
</form>
@endsection
