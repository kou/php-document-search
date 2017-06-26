<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <title>@yield('title')</title>
    </head>
    <body>
        <div class="container">
            @yield('content')
        </div>
    </body>
</html>