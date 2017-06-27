<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="UTF-8">
        <title>@yield('title')</title>
        <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
    </head>
    <body>
        <div class="container">
            @yield('content')
        </div>

        <script src="{{ mix('/js/app.js') }}"></script>
    </body>
</html>