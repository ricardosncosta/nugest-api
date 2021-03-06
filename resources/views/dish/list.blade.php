@extends('layouts.master')
@section('title', 'Dish: List')
@section('page_title', 'Dish: List')

@section('content')
    <a href="{{ route('dish::create_get') }}" class="col-sm-offset-4 col-sm-4"><span class="glyphicon glyphicon-plus"></span>&nbsp;New Dish</a>
    <p>&nbsp;</p>

    @if (count($dishes) > 0)
        <ul class="item-list list-group col-sm-offset-4 col-sm-4">
            @foreach ($dishes as $dish)
                <li class="list-group-item">
                    <button item-id="{{ $dish->id }}" type="button" class="close glyphicon glyphicon-remove" aria-label="Close" aria-hidden="true"></button>
                    <a href="{{ route('dish::update_get', [ 'id' => $dish->id ]) }}">{{ $dish->name }}</a>
                </li>
            @endforeach
        </ul>
    @else
        No dishes found.
    @endif
@endsection

@section('extrajavascripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $('.item-list li button').click(function() {
                elmt = $(this);
                dishName = elmt.parent().find('a').text();
                if (confirm("Remove dish '"+dishName+"'?")) {
                    $.ajax({
                        url: '/user/dishes/delete/' + elmt.attr('item-id'),
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
