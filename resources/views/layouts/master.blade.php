<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title>{{ Config::get('app.name') }} - @yield('title')</title>

        <!-- Stylesheets -->
        @section('stylesheets')
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
            <link rel="stylesheet" href="/css/default.css">
        @show

        <!-- Javascripts -->
        @section('javascripts')
            <script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
        @show

        <!-- Meta -->
        <meta name="viewport" content="width=device-width, initial-scale=1 user-scalable=no">
    </head>
    <body>
        <!-- header -->
        @include('layouts.partials.header')

        <div class="container" role="main">
            <h1>@yield('page_title')</h1>

            <!-- Validation errors -->
            <p>&nbsp;</p>
            @if (count($errors) > 0)
            	<div id="error-messages" class="container">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li class="alert alert-danger col-sm-offset-3 col-sm-5">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <p>&nbsp;</p>
            @endif

            @yield('content')
        </div>

        @include('layouts.partials.footer')

        <!-- Extra javascripts -->
        @section('extrajavascripts')
        @show
    </body>
</html>
