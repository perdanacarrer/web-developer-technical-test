@php
    $poster = (!empty($movie['Poster']) && $movie['Poster'] !== 'N/A') ? $movie['Poster'] : 'https://placehold.co/300x445?text=No+Poster';
@endphp
<div class="col-6 col-md-4 col-lg-3 movie-col" data-imdb-id="{{ $movie['imdbID'] }}">
    <div class="card h-100 shadow-sm movie-card">
        <a href="{{ route('movies.show', $movie['imdbID']) }}">
            <img src="{{ $poster }}" loading="lazy" class="card-img-top" alt="{{ $movie['Title'] }}" style="height:320px;object-fit:cover;" onerror="this.onerror=null;this.src='https://placehold.co/300x445?text=No+Poster';">
        </a>
        <div class="card-body d-flex flex-column">
            <h6 class="card-title mb-1">
                <a href="{{ route('movies.show', $movie['imdbID']) }}" class="text-decoration-none text-dark">{{ $movie['Title'] }}</a>
            </h6>
            <p class="text-muted small mb-2">{{ $movie['Year'] }} &middot; {{ ucfirst($movie['Type']) }}</p>
            <button
                class="btn btn-sm mt-auto favorite-btn {{ $isFavorite ? 'btn-danger' : 'btn-outline-danger' }}"
                data-imdb-id="{{ $movie['imdbID'] }}"
                data-title="{{ $movie['Title'] }}"
                data-year="{{ $movie['Year'] }}"
                data-poster="{{ $poster }}"
                data-type="{{ $movie['Type'] }}"
                data-favorited="{{ $isFavorite ? '1' : '0' }}">
                {{ $isFavorite ? __('messages.remove_from_favorites') : __('messages.add_to_favorites') }}
            </button>
        </div>
    </div>
</div>
