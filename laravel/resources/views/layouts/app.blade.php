<!DOCTYPE html>
<html lang="en" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Mora Lenz')</title>
        <meta name="description" content="@yield('description', 'Mora Lenz Official Website')">

        <link rel="icon" href="{{ asset('favicon.ico') }}">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-white text-gray-950 antialiased transition-colors duration-300 dark:bg-black dark:text-white">
        @include('partials.navbar')

        @yield('content')
    </body>
</html>
