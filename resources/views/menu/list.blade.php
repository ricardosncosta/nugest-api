@extends('layouts.master')
@section('title', 'Menu: List')
@section('page_title', 'Menu: List')

@section('content')
    <a href="{{ route('menu::create_get') }}" class="col-sm-offset-4 col-sm-4"><span class="glyphicon glyphicon-plus"></span>&nbsp;New Menu</a>
    <p>&nbsp;</p>
    @if (count($menus) > 0)
        <ul class="item-list list-group col-sm-offset-4 col-sm-4">
            <?php $menuCount = count($menus) ?>
            @for ($i = 0; $i < $menuCount; $i++)
                @if (!isset($menus[$i-1]) OR $menus[$i-1]->datetime->format('d') > $menus[$i]->datetime->format('d'))
                    <li class="list-group-item active">
                        {{ $menus[$i]->datetime->format('l, d') }}
                    </li>
                @endif

                <li class="list-group-item">
                    <button item-id="{{ $menus[$i]->id }}" type="button" class="close glyphicon glyphicon-remove" aria-label="Close" aria-hidden="true"></button>
                    <a href="{{ route('menu::update_get', [ 'id' => $menus[$i]->id ]) }}">{{ $menus[$i]->datetime->format('H:i') }} - {{ $menus[$i]->dish->name }}</a>
                </li>
            @endfor
        </ul>
    @else
        No menus found.
    @endif
@endsection

@section('extrajavascripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $('.item-list li button').click(function() {
                elmt = $(this);
                menuName = elmt.parent().find('a').text();
                if (confirm("Remove menu '"+menuName+"'?")) {
                    $.ajax({
                        url: '/user/menus/delete/' + elmt.attr('item-id'),
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
