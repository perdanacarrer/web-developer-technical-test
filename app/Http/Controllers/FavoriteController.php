<?php

namespace App\Http\Controllers;

use App\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * Favorite Movie page — lists everything saved so far, with an
     * empty-state layout when nothing has been added yet.
     */
    public function index()
    {
        $favorites = Favorite::orderByDesc('created_at')->get();

        return view('favorites.index', compact('favorites'));
    }

    /**
     * Add a movie to favorites (called via AJAX from both the List Movie
     * and Detail Movie pages).
     */
    public function store(Request $request)
    {
        $request->validate([
            'imdb_id' => 'required|string',
            'title' => 'required|string',
            'year' => 'nullable|string',
            'poster' => 'nullable|string',
            'type' => 'nullable|string',
        ]);

        $favorite = Favorite::firstOrCreate(
            ['imdb_id' => $request->imdb_id],
            $request->only('title', 'year', 'poster', 'type')
        );

        return response()->json([
            'status' => 'added',
            'favorite' => $favorite,
            'message' => __('messages.added_to_favorites'),
        ]);
    }

    /**
     * Remove a favorite (used from both the List/Detail pages and the
     * Favorite Movie page itself).
     */
    public function destroy(string $imdbId)
    {
        Favorite::where('imdb_id', $imdbId)->delete();

        return response()->json([
            'status' => 'removed',
            'message' => __('messages.removed_from_favorites'),
        ]);
    }
}
