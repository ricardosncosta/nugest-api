@extends('layouts.master')
@section('title', 'Dish: Create')
@section('page_title', 'Dish: Create')
@section('content')
<form method="POST" action="{{ route('dish::create_post')}}" class="form-horizontal">
    {!! csrf_field() !!}

    <!-- Name -->
    <div class="form-group">
        <label for="name" class="col-sm-offset-2 col-sm-3 control-label">Name</label>
        <div class="col-sm-3">
            <input type="text" name="name" id="name" placeholder="Name" class="form-control" value="{{ old('name') }}">
        </div>
    </div>
    <div class="form-group">
        <label for="calories" class="col-sm-offset-2 col-sm-3 control-label">Calories</label>
        <div class="col-sm-3">
            <input type="text" name="calories" id="calories" placeholder="Calories" class="form-control" value="{{ old('first_name') }}">
        </div>
    </div>

    <!-- Submit -->
    <div class="form-group">
        <div class="col-sm-offset-5 col-sm-3">
            <button type="submit" class="btn btn-default">Create</button>
        </div>
    </div>
</form>
@endsection
