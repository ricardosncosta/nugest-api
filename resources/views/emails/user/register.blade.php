@@extends('emails.layout')
@section('title', 'Hello!')
@section('content')
	<p>
		We need to confirm your email address.<br />
		Please click <a href="{{ route('user::email_confirmation_get', array($emailChange->email, $emailChange->token)) }}">here</a> to activate your account.<br /><br />
		Or copy and paste the following address on your browser {{ route('user::email_confirmation_get', array($emailChange->email, $emailChange->token)) }}
	</p>
@endsection
