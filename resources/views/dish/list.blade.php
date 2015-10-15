@extends('layouts.master')
@section('title', 'Dish: List')
@section('page_title', 'Dish: List')

@section('content')
    @if (count($dishes) > 0)
        <ul class="list-group col-sm-offset-4 col-sm-4">
            @foreach ($dishes as $dish)
                <li class="list-group-item">
                    <a href="{{ route('dish::update_get', [ 'id' => $dish->id ]) }}">{{ $dish->name }}</a>
                </li>
            @endforeach
        </ul>
    @else
        No dishes found. <a href="{{ route('dish::create_get') }}">Create one?</a>
    @endif
@endsection
