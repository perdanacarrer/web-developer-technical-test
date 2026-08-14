@extends('layouts.app')

@section('title', __('messages.nav_movies').' - '.__('messages.app_name'))

@section('content')
<form method="GET" action="{{ route('movies.index') }}" id="search-form" class="row g-2 mb-4">
    <div class="col-md-5">
        <input type="text" name="title" id="title-input" value="{{ $title }}" class="form-control" placeholder="{{ __('messages.search_placeholder') }}" required>
    </div>
    <div class="col-md-3">
        <select name="type" id="type-input" class="form-select">
            <option value="" {{ $type === '' ? 'selected' : '' }}>{{ __('messages.filter_all_types') }}</option>
            <option value="movie" {{ $type === 'movie' ? 'selected' : '' }}>{{ __('messages.filter_movie') }}</option>
            <option value="series" {{ $type === 'series' ? 'selected' : '' }}>{{ __('messages.filter_series') }}</option>
            <option value="episode" {{ $type === 'episode' ? 'selected' : '' }}>{{ __('messages.filter_episode') }}</option>
        </select>
    </div>
    <div class="col-md-2">
        <input type="number" name="year" id="year-input" value="{{ $year }}" class="form-control" placeholder="{{ __('messages.filter_year_placeholder') }}">
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100">{{ __('messages.search_button') }}</button>
    </div>
</form>

@if($searched)
    <p class="text-muted" id="results-count">{{ __('messages.results_count', ['count' => $totalResults]) }}</p>
@endif

<div class="row g-4" id="movie-grid">
    @foreach($results as $movie)
        @include('partials.movie-card', ['movie' => $movie, 'isFavorite' => in_array($movie['imdbID'], $favoriteIds)])
    @endforeach
</div>

@if($searched && count($results) === 0)
    @include('partials.empty-state', ['title' => __('messages.empty_no_results_title'), 'body' => __('messages.empty_no_results_body')])
@elseif(!$searched)
    @include('partials.empty-state', ['title' => __('messages.empty_search_title'), 'body' => __('messages.empty_search_body')])
@endif

<div id="infinite-scroll-sentinel" class="text-center text-muted py-4 d-none">
    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
    <span id="scroll-status-text">{{ __('messages.loading_more') }}</span>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const state = {
        title: @json($title),
        type: @json($type),
        year: @json($year),
        page: 1,
        totalResults: @json($totalResults),
        loading: false,
        favoriteIds: @json($favoriteIds),
    };

    const grid = document.getElementById('movie-grid');
    const sentinel = document.getElementById('infinite-scroll-sentinel');
    const statusText = document.getElementById('scroll-status-text');

    function cardHtml(movie, isFavorite) {
        const poster = (movie.Poster && movie.Poster !== 'N/A') ? movie.Poster : 'https://placehold.co/300x445?text=No+Poster';
        return `
        <div class="col-6 col-md-4 col-lg-3 movie-col" data-imdb-id="${movie.imdbID}">
            <div class="card h-100 shadow-sm movie-card">
                <a href="/movies/${movie.imdbID}">
                    <img src="${poster}" loading="lazy" class="card-img-top" alt="${movie.Title}" style="height:320px;object-fit:cover;" onerror="this.onerror=null;this.src='https://placehold.co/300x445?text=No+Poster';">
                </a>
                <div class="card-body d-flex flex-column">
                    <h6 class="card-title mb-1">
                        <a href="/movies/${movie.imdbID}" class="text-decoration-none text-dark">${movie.Title}</a>
                    </h6>
                    <p class="text-muted small mb-2">${movie.Year} &middot; ${movie.Type}</p>
                    <button
                        class="btn btn-sm mt-auto favorite-btn ${isFavorite ? 'btn-danger' : 'btn-outline-danger'}"
                        data-imdb-id="${movie.imdbID}"
                        data-title="${movie.Title.replace(/"/g, '&quot;')}"
                        data-year="${movie.Year}"
                        data-poster="${poster}"
                        data-type="${movie.Type}"
                        data-favorited="${isFavorite ? '1' : '0'}">
                        ${isFavorite ? window.APP_LOCALE_STRINGS.removeFromFavorites : window.APP_LOCALE_STRINGS.addToFavorites}
                    </button>
                </div>
            </div>
        </div>`;
    }

    async function toggleFavorite(btn) {
        const imdbId = btn.dataset.imdbId;
        const favorited = btn.dataset.favorited === '1';

        if (favorited) {
            const res = await fetch(`/favorites/${imdbId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json' },
            });
            const data = await res.json();
            btn.dataset.favorited = '0';
            btn.classList.remove('btn-danger');
            btn.classList.add('btn-outline-danger');
            btn.textContent = window.APP_LOCALE_STRINGS.addToFavorites;
            showToast(data.message);
        } else {
            const res = await fetch('/favorites', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': window.CSRF_TOKEN,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    imdb_id: imdbId,
                    title: btn.dataset.title,
                    year: btn.dataset.year,
                    poster: btn.dataset.poster,
                    type: btn.dataset.type,
                }),
            });
            const data = await res.json();
            btn.dataset.favorited = '1';
            btn.classList.remove('btn-outline-danger');
            btn.classList.add('btn-danger');
            btn.textContent = window.APP_LOCALE_STRINGS.removeFromFavorites;
            showToast(data.message);
        }
    }

    grid.addEventListener('click', function (e) {
        const btn = e.target.closest('.favorite-btn');
        if (btn) toggleFavorite(btn);
    });

    async function loadMore() {
        if (state.loading || !state.title) return;
        if (state.page * 10 >= state.totalResults && state.page > 1) return;

        state.loading = true;
        sentinel.classList.remove('d-none');
        statusText.textContent = window.APP_LOCALE_STRINGS.loadingMore;

        const nextPage = state.page + 1;
        const params = new URLSearchParams({ title: state.title, page: nextPage });
        if (state.type) params.set('type', state.type);
        if (state.year) params.set('year', state.year);

        try {
            const res = await fetch(`/movies/search?${params.toString()}`, { headers: { 'Accept': 'application/json' } });
            const data = await res.json();

            data.results.forEach(movie => {
                grid.insertAdjacentHTML('beforeend', cardHtml(movie, data.favoriteIds.includes(movie.imdbID)));
            });

            state.page = nextPage;
            state.totalResults = data.totalResults;

            if (!data.hasMore) {
                statusText.textContent = window.APP_LOCALE_STRINGS.noMoreResults;
            } else {
                sentinel.classList.add('d-none');
            }
        } finally {
            state.loading = false;
        }
    }

    if ('IntersectionObserver' in window && state.title) {
        const observer = new IntersectionObserver(entries => {
            if (entries[0].isIntersecting) loadMore();
        }, { rootMargin: '300px' });
        observer.observe(sentinel);
    }
})();
</script>
@endpush
