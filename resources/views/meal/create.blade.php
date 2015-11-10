@extends('layouts.master')
@section('title', 'Meal: Create')
@section('page_title', 'Meal: Create')
@section('content')
<form method="POST" action="{{ route('meal::create_post')}}" class="form-horizontal">
    {!! csrf_field() !!}

    @if (count($dishes) > 0)
        <!-- Dish -->
        <div class="form-group">
            <label for="dish" class="col-sm-offset-2 col-sm-3 control-label">Dish</label>
            <div class="col-sm-3">
                <select class="form-control" name="dish">
                    @foreach ($dishes as $dish)
                        <option value="{{ $dish->id }}">
                            {{ $dish->name }} @if ($dish->calories != '') ({{ $dish->calories }} kCal) @endif
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    @endif

    <!-- Submit -->
    <div class="form-group">
        <div class="col-sm-offset-5 col-sm-3">
            <button type="submit" class="btn btn-default">Create</button>
        </div>
    </div>
</form>
@endsection
