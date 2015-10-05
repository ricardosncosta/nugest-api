@extends('layouts.master')
@section('title', 'Account: Update')
@section('page_title', 'Account: Update')
@section('content')
<form method="POST" action="{{ route('user::update_post')}}" class="form-horizontal">
    {!! csrf_field() !!}

    <!-- First and Last name -->
    <div class="form-group">
        <label for="email" class="col-sm-offset-2 col-sm-3 control-label">First name</label>
        <div class="col-sm-3">
            <input type="text" name="first_name" id="first_name" placeholder="First name" class="form-control" value="{{ $user->first_name }}">
        </div>
    </div>
    <div class="form-group">
        <label for="last_name" class="col-sm-offset-2 col-sm-3 control-label">Last name</label>
        <div class="col-sm-3">
            <input type="text" name="last_name" id="last_name" placeholder="Last name" class="form-control" value="{{ $user->last_name }}">
        </div>
    </div>

    <!-- Submit -->
    <div class="form-group">
        <div class="col-sm-offset-5 col-sm-3">
			<p>
				<a href="{{ route('user::update_email_get') }}">Change Password</a>
			</p>
			<p>
				<a href="{{ route('user::update_email_get') }}">Change email address</a>
			</p>
			<p>&nbsp;</p>
            <button type="submit" class="btn btn-default">Update</button>
        </div>
    </div>
</form>
@endsection
