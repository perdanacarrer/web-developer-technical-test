<?php

namespace App\Http\Controllers;

use App\Favorite;
use Illuminate\Http\Request;
use App\Services\OmdbService;

class MovieController extends Controller
{
    protected $omdb;

    public function __construct(OmdbService $omdb)
    {
        $this->omdb = $omdb;
    }

    /**
     * List Movie page. Renders the search form and the first page of
     * results server-side; further pages are appended client-side via
     * infinite scroll against search().
     */
    public function index(Request $request)
    {
        $title = $request->query('title', '');
        $type = $request->query('type', '');
        $year = $request->query('year', '');

        $results = [];
        $totalResults = 0;
        $searched = $title !== '';

        if ($searched) {
            $data = $this->omdb->search($title, 1, $type ?: null, $year ?: null);

            if (($data['Response'] ?? 'False') === 'True') {
                $results = $data['Search'];
                $totalResults = (int) ($data['totalResults'] ?? 0);
            }
        }

        $favoriteIds = Favorite::pluck('imdb_id')->toArray();

        return view('movies.index', compact('results', 'totalResults', 'title', 'type', 'year', 'searched', 'favoriteIds'));
    }

    /**
     * JSON endpoint consumed by the infinite-scroll JS on the List Movie page.
     */
    public function search(Request $request)
    {
        $title = $request->query('title', '');
        $type = $request->query('type', '');
        $year = $request->query('year', '');
        $page = max(1, (int) $request->query('page', 1));

        if ($title === '') {
            return response()->json(['results' => [], 'totalResults' => 0, 'hasMore' => false]);
        }

        $data = $this->omdb->search($title, $page, $type ?: null, $year ?: null);
        $results = ($data['Response'] ?? 'False') === 'True' ? $data['Search'] : [];
        $totalResults = (int) ($data['totalResults'] ?? 0);
        $hasMore = ($page * 10) < $totalResults;

        $favoriteIds = Favorite::pluck('imdb_id')->toArray();

        return response()->json([
            'results' => $results,
            'totalResults' => $totalResults,
            'hasMore' => $hasMore,
            'favoriteIds' => $favoriteIds,
        ]);
    }

    /**
     * Movie Detail page.
     */
    public function show(string $imdbId)
    {
        $movie = $this->omdb->find($imdbId);

        if (($movie['Response'] ?? 'False') !== 'True') {
            abort(404);
        }

        $isFavorite = Favorite::where('imdb_id', $imdbId)->exists();

        return view('movies.show', compact('movie', 'isFavorite'));
    }
}
