<?php

return [

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /*
     * OMDb API — http://www.omdbapi.com/
     * Used by App\Services\OmdbService for search, listing and detail pages.
     */
    'omdb' => [
        'base_url' => env('OMDB_BASE_URL', 'http://www.omdbapi.com/'),
        'key' => env('OMDB_API_KEY'),
    ],

];
