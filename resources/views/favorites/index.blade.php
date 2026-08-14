@extends('layouts.app')

@section('title', __('messages.nav_favorites').' - '.__('messages.app_name'))

@section('content')
<h4 class="mb-4">{{ __('messages.nav_favorites') }}</h4>

<div class="row g-4" id="favorites-grid">
    @foreach($favorites as $fav)
        <div class="col-6 col-md-4 col-lg-3 movie-col" data-imdb-id="{{ $fav->imdb_id }}">
            <div class="card h-100 shadow-sm movie-card">
                <a href="{{ route('movies.show', $fav->imdb_id) }}">
                    <img src="{{ $fav->poster ?: 'https://placehold.co/300x445?text=No+Poster' }}" loading="lazy" class="card-img-top" alt="{{ $fav->title }}" style="height:320px;object-fit:cover;" onerror="this.onerror=null;this.src='https://placehold.co/300x445?text=No+Poster';">
                </a>
                <div class="card-body d-flex flex-column">
                    <h6 class="card-title mb-1">
                        <a href="{{ route('movies.show', $fav->imdb_id) }}" class="text-decoration-none text-dark">{{ $fav->title }}</a>
                    </h6>
                    <p class="text-muted small mb-2">{{ $fav->year }} &middot; {{ ucfirst($fav->type) }}</p>
                    <button class="btn btn-sm btn-danger mt-auto remove-favorite-btn" data-imdb-id="{{ $fav->imdb_id }}">
                        {{ __('messages.remove_from_favorites') }}
                    </button>
                </div>
            </div>
        </div>
    @endforeach
</div>

@if($favorites->isEmpty())
    @include('partials.empty-state', ['title' => __('messages.empty_favorites_title'), 'body' => __('messages.empty_favorites_body')])
@endif
@endsection

@push('scripts')
<script>
document.getElementById('favorites-grid').addEventListener('click', async function (e) {
    const btn = e.target.closest('.remove-favorite-btn');
    if (!btn) return;

    const imdbId = btn.dataset.imdbId;
    const res = await fetch(`/favorites/${imdbId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json' },
    });
    const data = await res.json();
    const col = document.querySelector(`.movie-col[data-imdb-id="${imdbId}"]`);
    if (col) col.remove();
    showToast(data.message);

    if (!document.querySelector('.movie-col')) {
        location.reload();
    }
});
</script>
@endpush
