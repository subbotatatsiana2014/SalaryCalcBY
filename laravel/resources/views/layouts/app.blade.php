<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SalaryCalc BY')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    @stack('styles')
</head>
<body>
@if(auth()->check())
    @include('components.sidebar')

    <div class="main-content">
        @include('components.header')
        <main class="content-wrapper">
            @yield('content')
        </main>
    </div>

    <div class="sidebar-overlay"></div>
@else
    @yield('content')
@endif

<script src="{{ asset('js/app.js') }}?v={{ time() }}"></script>

@stack('scripts')
</body>
</html>
