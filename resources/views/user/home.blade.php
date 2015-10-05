@extends('layouts.master')
@section('title','Home')
@section('content')
	<h1>{{ $greeting }}, {{{ $user->first_name }}}.</h1>

	<div class="jumbotron text-center">
		<h2>{{ $user->email }}</h2>
		<p>
			<strong>First name:</strong> {{ $user->first_name }}<br>
			<strong>Last name:</strong> {{ $user->last_name }}<br>
		</p>
	</div>
@stop
