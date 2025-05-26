<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-f8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'エンジニア検索システム')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- <link href="{{ asset('css/style.css') }}" rel="stylesheet"> --}}
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ route('engineers.searchForm') }}">エンジニア検索</a>
            {{-- 必要に応じてナビゲーションリンクを追加 --}}
        </div>
    </nav>

    <main class="container">
        @yield('content')
    </main>

    <footer class="text-center mt-5 py-3 bg-light">
        <p>&copy; {{ date('Y') }} 人材管理システム</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
