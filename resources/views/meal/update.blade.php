@extends('layouts.master')
@section('title', 'Meal: Update')
@section('page_title', 'Meal: Update')
@section('content')
<form method="POST" action="{{ route('meal::update_post', ['id' => $meal->id]) }}" class="form-horizontal">
    {!! csrf_field() !!}

    <!-- Dish -->
    <div class="form-group">
        <label for="dish" class="col-sm-offset-2 col-sm-3 control-label">Dish</label>
        <div class="col-sm-3">
            <select class="form-control" name="dish">
                @foreach ($dishes as $dish)
                    <option value="{{ $dish->id }}" @if ($dish->id == $meal->dish_id) selected @endif>
                        {{ $dish->name }} @if ($dish->calories != '') ({{ $dish->calories }} kCal) @endif
                    </option>
                @endforeach
            </select>
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
