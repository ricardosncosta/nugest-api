@extends('layouts.master')
@section('title', 'Menu: Update')
@section('page_title', 'Menu: Update')
@section('content')
<form method="POST" action="{{ route('menu::update_post', ['id' => $menu->id]) }}" class="form-horizontal">
    {!! csrf_field() !!}

    <!-- Dish -->
    <div class="form-group">
        <label for="dish" class="col-sm-offset-2 col-sm-3 control-label">Dish</label>
        <div class="col-sm-3">
            <select class="form-control" name="dish">
                @foreach ($dishes as $dish)
                    <option value="{{ $dish->id }}" @if ($dish->id == $menu->dish_id) selected @endif>
                        {{ $dish->name }} @if ($dish->calories != '') ({{ $dish->calories }} kCal) @endif
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="name" class="col-sm-offset-2 col-sm-3 control-label">Date &amp; Time</label>
        <div class="col-sm-3">
            <input type="text" name="datetime" id="datetime" placeholder="Date &amp; Time" class="form-control" value="{{ $menu->datetime }}">
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
