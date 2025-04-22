<?php

namespace App\Command;

use App\Service\TmdbApiService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:tmdb-data',
    description: 'Récupère les données depuis l’API TMDB'
)]
class TmdbDataCommand extends Command
{
    protected static $defaultName = 'app:tmdb-data';

    private TmdbApiService $tmdbApiService;

    public function __construct(TmdbApiService $tmdbApiService)
    {
        parent::__construct();
        $this->tmdbApiService = $tmdbApiService;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Récupère les données depuis l’API TMDB et les enregistre en local.')
            ->setHelp('Cette commande permet de sauvegarder les films populaires et par genre depuis TMDB.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('🎬 Récupération des films populaires...');
        $popularMovies = $this->tmdbApiService->fetchPopularMovies();
        file_put_contents('var/tmdb/popularMovies.json', json_encode($popularMovies, JSON_PRETTY_PRINT));

        $output->writeln('📽️ Récupération de tous les films...');
        $allMovies = $this->tmdbApiService->fetchAllMovies();
        file_put_contents('var/tmdb/allMovies.json', json_encode($allMovies, JSON_PRETTY_PRINT));

        $output->writeln('🎞️ Récupération des films par genre...');
        $genres = [
            "28" => "Action",
            "12" => "Aventure",
            "10752" => "Guerre",
            "99" => "Documentaire",
            "18" => "Drame",
            "10751" => "Famille",
            "14" => "Fantasy",
            "36" => "Histoire",
            "27" => "Horreur",
            "10402" => "Musique",
            "9648" => "Mystère",
            "10749" => "Romance",
            "878" => "Science-Fiction",
            "53" => "Thriller",
        ];

        foreach ($genres as $genreId => $label) {
            $output->writeln("  ➜ $label...");
            $movies = $this->tmdbApiService->fetchGenreMovies($genreId);
            file_put_contents("var/tmdb/movies_genre_{$genreId}.json", json_encode($movies, JSON_PRETTY_PRINT));
        }

        $output->writeln('✅ Données TMDB enregistrées dans var/tmdb/');
        return Command::SUCCESS;
    }
}
