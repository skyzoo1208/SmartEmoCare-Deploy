<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title', 'CalmMind')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto py-8">
        @yield('content')
    </div>

    @stack('scripts')
    <!-- Load Script Model -->
    <script src="{{ asset('tf_model/model_script.js') }}"></script>
</body>
</html>
