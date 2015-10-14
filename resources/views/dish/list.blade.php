@extends('layouts.master')
@section('title', 'Dish: List')
@section('page_title', 'Dish: List')

@section('content')
    @if (count($dishes) > 0)
        <ul class="list-group">
            @foreach ($dishes as $dish)
                <li class="list-group-item">{{ $dish->name }}</li>
            @endforeach
        </ul>
    @else
        No dishes found. <a href="{{ route('dish::create_get') }}">Create one?</a>
    @endif
@endsection
