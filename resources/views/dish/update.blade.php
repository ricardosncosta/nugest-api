@extends('layouts.master')
@section('title', 'Dish: Update')
@section('page_title', 'Dish: Update')
@section('content')
<form method="POST" action="{{ route('dish::update_post', ['id' => $dish->id])}}" class="form-horizontal">
    {!! csrf_field() !!}

    <!-- First and Last name -->
    <div class="form-group">
        <label for="name" class="col-sm-offset-2 col-sm-3 control-label">Name</label>
        <div class="col-sm-3">
            <input type="text" name="name" id="name" placeholder="Name" class="form-control" value="{{ $dish->name }}">
        </div>
    </div>
    <div class="form-group">
        <label for="calories" class="col-sm-offset-2 col-sm-3 control-label">Calories</label>
        <div class="col-sm-3">
            <input type="text" name="calories" id="calories" placeholder="Calories" class="form-control" value="{{ $dish->calories }}">
        </div>
    </div>

    <!-- Submit -->
    <div class="form-group">
        <div class="col-sm-offset-5 col-sm-3">
            <button type="submit" class="btn btn-default">Update</button>
        </div>
    </div>
</form>
@endsection
