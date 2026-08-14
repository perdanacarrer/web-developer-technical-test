<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * A user's saved/favorite movie.
 *
 * The test brief only defines a single hardcoded credential pair rather
 * than a real multi-user system, so favorites are stored globally
 * (no user_id column) and persisted in SQLite so the list survives
 * across sessions and page reloads.
 */
class Favorite extends Model
{
    protected $fillable = [
        'imdb_id', 'title', 'year', 'poster', 'type',
    ];
}
