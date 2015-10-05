@extends('layouts.master')

@section('title', 'Account: Update email')
@section('page_title', 'Account: Update email')

@section('content')
<form method="POST" action="{{ route('user::update_email_post')}}" class="form-horizontal">
    {!! csrf_field() !!}

    <!-- Password -->
    <div class="form-group">
        <label for="current_password" class="col-sm-offset-2 col-sm-3 control-label">Current Password</label>
        <div class="col-sm-3">
            <input type="password" name="current_password" id="current_password" placeholder="Current Password" class="form-control">
        </div>
    </div>

	<p>&nbsp;</p>

    <!-- Email -->
    <div class="form-group">
        <label for="email" class="col-sm-offset-2 col-sm-3 control-label">Email</label>
        <div class="col-sm-3">
            <input type="email" name="email" id="email" placeholder="Email address" class="form-control">
        </div>
    </div>

    <!-- Email confirmation -->
    <div class="form-group">
        <label for="email_confirmation" class="col-sm-offset-2 col-sm-3 control-label">Email Confirmation</label>
        <div class="col-sm-3">
            <input type="email" name="email_confirmation" id="email_confirmation" placeholder="Email address confirmation" class="form-control">
        </div>
    </div>

    <!-- Submit -->
    <div class="form-group">
        <div class="col-sm-offset-5 col-sm-3">
            <button type="submit" class="btn btn-default">Update email</button>
        </div>
    </div>
</form>
@endsection
