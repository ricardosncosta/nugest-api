@extends('layouts.master')
@section('title', 'Meal: List')
@section('page_title', 'Meal: List')

@section('content')
    <a href="{{ route('meal::create_get') }}" class="col-sm-offset-4 col-sm-4"><span class="glyphicon glyphicon-plus"></span>&nbsp;New Meal</a>
    <p>&nbsp;</p>
    @if (count($meals) > 0)
        <ul class="item-list list-group col-sm-offset-4 col-sm-4">
            <?php $mealCount = count($meals) ?>
            @for ($i = 0; $i < $mealCount; $i++)
                @if (!isset($meals[$i-1]) OR $meals[$i-1]->datetime->format('d') > $meals[$i]->datetime->format('d'))
                    <li class="list-group-item active">
                        {{ $meals[$i]->datetime->format('l, d') }}
                    </li>
                @endif

                <li class="list-group-item">
                    <button item-id="{{ $meals[$i]->id }}" type="button" class="close glyphicon glyphicon-remove" aria-label="Close" aria-hidden="true"></button>
                    <a href="{{ route('meal::update_get', [ 'id' => $meals[$i]->id ]) }}">{{ $meals[$i]->datetime->format('H:i') }} - {{ $meals[$i]->dish->name }}</a>
                </li>
            @endfor
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
