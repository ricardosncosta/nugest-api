@extends('layouts.master')
@section('title', 'Meal: List')
@section('page_title', 'Meal: List')

@section('content')
    <a href="{{ route('dish::create_get') }}" class="col-sm-offset-4 col-sm-4"><span class="glyphicon glyphicon-plus"></span>&nbsp;New Meal</a>
    <p>&nbsp;</p>

    @if (count($meals) > 0)
        <ul class="item-list list-group col-sm-offset-4 col-sm-4">
            @foreach ($meals as $meal)
                <li class="list-group-item">
                    <button item-id="{{ $meal->id }}" type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <a href="{{ route('meal::update_get', [ 'id' => $meal->id ]) }}">{{ $meal->dish->name }}</a>
                </li>
            @endforeach
        </ul>
    @else
        No meals found.
    @endif
@endsection

@section('extrajavascripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $('.item-list li button').click(function() {
                elmt = $(this);
                mealName = elmt.parent().find('a').text();
                if (confirm("Remove meal '"+mealName+"'?")) {
                    $.ajax({
                        url: '/user/meals/delete/' + elmt.attr('item-id'),
                        dataType: 'json',
                    })
                    .done(function(data) {
                        if (data.status === 'success') {
                            elmt.parent().slideUp();
                        } else {
                            alert( "Something went wrong, please try again." );
                        }
                    })
                    .fail(function() {
                        alert( "Something went wrong, please try again." );
                    })
                }
            });
        });
    </script>
@endsection
