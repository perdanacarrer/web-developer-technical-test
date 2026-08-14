<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

/**
 * Thin wrapper around the OMDb API (http://www.omdbapi.com/).
 *
 * Keeping all HTTP concerns here (instead of inside the controller) means
 * MovieController only has to think in terms of "search movies" / "get one
 * movie" and never touches Guzzle or the raw OMDb response shape directly.
 */
class OmdbService
{
    /** @var Client */
    protected $client;

    /** @var string */
    protected $apiKey;

    public function __construct(string $baseUrl, ?string $apiKey)
    {
        $this->client = new Client([
            'base_uri' => $baseUrl,
            'timeout' => 8,
        ]);

        $this->apiKey = $apiKey;
    }

    /**
     * Search movies by title with optional type/year filters.
     * Mirrors OMDb's "s=" search endpoint, which returns up to 10 results
     * per page.
     */
    public function search(string $title, int $page = 1, ?string $type = null, ?string $year = null): array
    {
        $query = array_filter([
            'apikey' => $this->apiKey,
            's' => $title,
            'page' => $page,
            'type' => $type,
            'y' => $year,
        ]);

        return $this->request($query);
    }

    /**
     * Fetch full details for a single title by its IMDb ID.
     */
    public function find(string $imdbId): array
    {
        return $this->request([
            'apikey' => $this->apiKey,
            'i' => $imdbId,
            'plot' => 'full',
        ]);
    }

    protected function request(array $query): array
    {
        try {
            $response = $this->client->get('', ['query' => $query]);
            $data = json_decode((string) $response->getBody(), true) ?? [];
        } catch (\Throwable $e) {
            Log::error('OMDb API request failed: '.$e->getMessage());

            return ['Response' => 'False', 'Error' => 'Unable to reach OMDb API.'];
        }

        return $data;
    }
}
