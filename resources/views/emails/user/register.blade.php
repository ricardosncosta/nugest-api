@extends('emails.layout')
@section('title', 'Welcome to Nugest!')
@section('content')
	<p>
		Hello {{ $user->first_name }} {{ $user->last_name }} and welcome!<br/><br/>
		Please click <a href="{{ route('email_confirmation', array($emailChange->email, $emailChange->token)) }}">here</a> to activate your account
		or copy and paste the following address on your browser: {{ route('email_confirmation', array($emailChange->email, $emailChange->token)) }}
	</p>
@endsection
