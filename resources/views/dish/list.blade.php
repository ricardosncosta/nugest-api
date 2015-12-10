@extends('layouts.master')
@section('title', 'Dish: List')
@section('page_title', 'Dish: List')

@section('content')
    @if (count($dishes) > 0)
        <ul class="item-list list-group col-sm-offset-4 col-sm-4">
            @foreach ($dishes as $dish)
                <li class="list-group-item">
                    <button item-id="{{ $dish->id }}" type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <a href="{{ route('dish::update_get', [ 'id' => $dish->id ]) }}">{{ $dish->name }}</a>
                </li>
            @endforeach
        </ul>
    @else
        No dishes found. <a href="{{ route('dish::create_get') }}">Create one?</a>
    @endif
@endsection

@section('extrajavascripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $('.item-list li button').click(function() {
                elmt = $(this);
                if (confirm("Remove dish '"+elmt.parent().find('a').text()+"'?")) {
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
