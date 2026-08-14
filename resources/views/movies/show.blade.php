@extends('layouts.app')

@section('title', ($movie['Title'] ?? __('messages.details')).' - '.__('messages.app_name'))

@section('content')
@php
    $poster = (!empty($movie['Poster']) && $movie['Poster'] !== 'N/A') ? $movie['Poster'] : 'https://placehold.co/400x593?text=No+Poster';
@endphp

<a href="{{ route('movies.index') }}" class="btn btn-link ps-0 mb-3">&larr; {{ __('messages.back_to_list') }}</a>

<div class="row g-4">
    <div class="col-md-4">
        <img src="{{ $poster }}" loading="lazy" class="img-fluid rounded shadow-sm" alt="{{ $movie['Title'] }}" onerror="this.onerror=null;this.src='https://placehold.co/400x593?text=No+Poster';">
    </div>
    <div class="col-md-8">
        <h2>{{ $movie['Title'] }} <small class="text-muted">({{ $movie['Year'] }})</small></h2>

        <button
            id="favorite-btn"
            class="btn mb-3 {{ $isFavorite ? 'btn-danger' : 'btn-outline-danger' }}"
            data-imdb-id="{{ $movie['imdbID'] }}"
            data-title="{{ $movie['Title'] }}"
            data-year="{{ $movie['Year'] }}"
            data-poster="{{ $poster }}"
            data-type="{{ $movie['Type'] ?? 'movie' }}"
            data-favorited="{{ $isFavorite ? '1' : '0' }}">
            {{ $isFavorite ? __('messages.remove_from_favorites') : __('messages.add_to_favorites') }}
        </button>

        <table class="table table-sm">
            <tr><th class="w-25">{{ __('messages.genre') }}</th><td>{{ $movie['Genre'] ?? __('messages.not_available') }}</td></tr>
            <tr><th>{{ __('messages.released') }}</th><td>{{ $movie['Released'] ?? __('messages.not_available') }}</td></tr>
            <tr><th>{{ __('messages.runtime') }}</th><td>{{ $movie['Runtime'] ?? __('messages.not_available') }}</td></tr>
            <tr><th>{{ __('messages.director') }}</th><td>{{ $movie['Director'] ?? __('messages.not_available') }}</td></tr>
            <tr><th>{{ __('messages.writer') }}</th><td>{{ $movie['Writer'] ?? __('messages.not_available') }}</td></tr>
            <tr><th>{{ __('messages.actors') }}</th><td>{{ $movie['Actors'] ?? __('messages.not_available') }}</td></tr>
            <tr><th>{{ __('messages.language') }}</th><td>{{ $movie['Language'] ?? __('messages.not_available') }}</td></tr>
            <tr><th>{{ __('messages.country') }}</th><td>{{ $movie['Country'] ?? __('messages.not_available') }}</td></tr>
            <tr><th>{{ __('messages.awards') }}</th><td>{{ $movie['Awards'] ?? __('messages.not_available') }}</td></tr>
            <tr><th>{{ __('messages.imdb_rating') }}</th><td>{{ $movie['imdbRating'] ?? __('messages.not_available') }} / 10</td></tr>
        </table>

        <h5>{{ __('messages.plot') }}</h5>
        <p>{{ $movie['Plot'] ?? __('messages.not_available') }}</p>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('favorite-btn').addEventListener('click', async function () {
    const btn = this;
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
});
</script>
@endpush
