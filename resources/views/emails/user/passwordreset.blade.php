@extends('emails.layout')
@section('title', 'Password reset request')
@section('content')
<p>
	A password reset was requested.
	<br><br>

	Please click <a href="{{ url('users/reset/'.$token) }}">here</a> to reset your password.
	<br><br>

	If this wasn't you, please ignore this message.
</p>
@endsection
