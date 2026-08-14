<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', __('messages.app_name'))</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    @if(session('logged_in'))
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ route('movies.index') }}">🎬 {{ __('messages.app_name') }}</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="nav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('movies.*') ? 'active' : '' }}" href="{{ route('movies.index') }}">{{ __('messages.nav_movies') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('favorites.*') ? 'active' : '' }}" href="{{ route('favorites.index') }}">{{ __('messages.nav_favorites') }}</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <div class="btn-group btn-group-sm me-3">
                        <a href="{{ route('lang.switch', 'en') }}" class="btn btn-outline-light {{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</a>
                        <a href="{{ route('lang.switch', 'id') }}" class="btn btn-outline-light {{ app()->getLocale() === 'id' ? 'active' : '' }}">ID</a>
                    </div>
                    <span class="text-light small me-3">{{ __('messages.welcome') }}, {{ session('username') }}</span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="btn btn-sm btn-outline-light">{{ __('messages.logout') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    @endif

    <div class="container pb-5">
        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @yield('content')
    </div>

    <div id="toast" class="toast align-items-center text-white bg-dark border-0 position-fixed bottom-0 end-0 m-4" role="alert" style="z-index:1080">
        <div class="d-flex">
            <div class="toast-body" id="toast-body"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.APP_LOCALE_STRINGS = {
            addToFavorites: @json(__('messages.add_to_favorites')),
            removeFromFavorites: @json(__('messages.remove_from_favorites')),
            loadingMore: @json(__('messages.loading_more')),
            noMoreResults: @json(__('messages.no_more_results')),
        };
        window.CSRF_TOKEN = @json(csrf_token());

        function showToast(message) {
            const toastEl = document.getElementById('toast');
            document.getElementById('toast-body').textContent = message;
            new bootstrap.Toast(toastEl).show();
        }
    </script>
    @stack('scripts')
</body>
</html>
