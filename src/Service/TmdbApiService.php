<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class TmdbApiService
{
    private HttpClientInterface $client;
    private string $apiKey;
    private string $projectDir;

    public function __construct(
        HttpClientInterface $client,
        string $apiKey,
        ParameterBagInterface $params
    ) {
        $this->client = $client;
        $this->apiKey = $apiKey;
        $this->projectDir = $params->get('kernel.project_dir');
    }

    private function getDataFile(string $relativePath, callable $apiCallback): array
    {
        $fullPath = $this->projectDir . '/' . $relativePath;
        echo "📁 Enregistrement du fichier : $fullPath\n";

        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        if (file_exists($fullPath)) {
            $data = json_decode(file_get_contents($fullPath), true);
            if ($data !== null) {
                return $data;
            }
        }

        $data = $apiCallback();
        file_put_contents($fullPath, json_encode($data, JSON_PRETTY_PRINT));
        return $data;
    }

    public function fetchPopularMovies(): array
    {
        return $this->getDataFile(
            "var/tmdb/popularMovies.json",
            fn() => $this->client->request('GET', 'https://api.themoviedb.org/3/movie/popular', [
                'query' => [
                    'api_key' => $this->apiKey,
                    'language' => 'fr-FR'
                ]
            ])->toArray()
        );
    }

    public function fetchAllMovies(): array
    {
        return $this->getDataFile(
            'var/tmdb/allMovies.json',
            fn() => $this->fetchAllPages('/movie/popular')
        );
    }

    private function fetchAllPages(string $endpoint): array
    {
        $allMovies = [];
        $page = 1;

        do {
            $response = $this->client->request('GET', "https://api.themoviedb.org/3$endpoint", [
                'query' => [
                    'api_key' => $this->apiKey,
                    'language' => 'fr-FR',
                    'page' => $page,
                ]
            ])->toArray();

            $allMovies = array_merge($allMovies, $response['results']);
            $totalPages = $response['total_pages'] ?? 1;
            $page++;
        } while ($page <= $totalPages);

        return $allMovies;
    }

    public function fetchGenreMovies(string $genreId): array
    {
        return $this->getDataFile(
            "var/tmdb/movies_genre_{$genreId}.json",
            fn() => $this->client->request('GET', 'https://api.themoviedb.org/3/discover/movie', [
                'query' => [
                    'api_key' => $this->apiKey,
                    'language' => 'fr-FR',
                    'with_genres' => $genreId,
                ]
            ])->toArray()
        );
    }

    public function fetchDetailMovie(int $id): array
    {
        $response = $this->client->request('GET', "https://api.themoviedb.org/3/movie/{$id}", [
            'query' => [
                'api_key' => $this->apiKey,
                'language' => 'fr-FR'
            ]
        ]);

        return $response->toArray();
    }

    public function videoMovie(int $id): array
    {
        return $this->client->request('GET', "https://api.themoviedb.org/3/movie/{$id}/videos", [
            'query' => [
                'api_key' => $this->apiKey,
                'language' => 'fr-FR'
            ]
        ])->toArray();
    }

    public function reviewMovie(int $id): array
    {
        return $this->client->request('GET', "https://api.themoviedb.org/3/movie/{$id}/reviews", [
            'query' => [
                'api_key' => $this->apiKey,
                'language' => 'fr-FR'
            ]
        ])->toArray();
    }

    public function searchMovies(string $query): array
    {
        return $this->client->request('GET', 'https://api.themoviedb.org/3/search/movie', [
            'query' => [
                'api_key' => $this->apiKey,
                'language' => 'fr-FR',
                'query' => $query
            ]
        ])->toArray();
    }
}
