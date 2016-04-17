@extends('layouts.master')
@section('title', 'Menu: Create')
@section('page_title', 'Menu: Create')
@section('content')
<form method="POST" action="{{ route('menu::create_post')}}" class="form-horizontal">
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
    <div class="form-group">
        <label for="name" class="col-sm-offset-2 col-sm-3 control-label">Date &amp; Time</label>
        <div class="col-sm-3">
            <input type="text" name="datetime" id="datetime" placeholder="Time" class="form-control" value="{{ date('H:i') }}">
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
