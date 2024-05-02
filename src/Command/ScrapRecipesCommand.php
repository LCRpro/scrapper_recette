<?php

// src/Command/ScrapRecipesCommand.php
namespace App\Command;

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Recette;
use App\Entity\Ingredient;
use App\Entity\Etape;

class ScrapRecipesCommand extends Command
{
    protected static $defaultName = 'app:recette';
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this->setName('app:recette')
            ->setDescription('Scrap recipes from a URL and insert data into the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $client = new HttpBrowser(HttpClient::create([
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3'
            ]
        ]));

        // Request the main page to extract links
        $crawler = $client->request('GET', 'https://www.allrecipes.com/recipes/77/drinks/');
        $links = $crawler->filter('div.mntl-taxonomysc-article-list-group a.mntl-card-list-items')->links();

        foreach ($links as $link) {
            $url = $link->getUri();
            
            $crawler = $client->request('GET', $url);
            
            // Extract recipe data
            $title = $crawler->filter('h1')->text();

            $recette = new Recette();
            $recette->setTitle($title);

            // Extract ingredients
            $ingredientItems = $crawler->filter('ul.mntl-structured-ingredients__list li')->each(function (Crawler $node) use ($recette) {
                $ingredient = new Ingredient();
                $ingredient->setContent($node->text());
                $ingredient->setRecette($recette);
                return $ingredient;
            });

            // Extract steps
            $stepsItems = $crawler->filter('div.recipe__steps-content ol li')->each(function (Crawler $node, $index) use ($recette) {
                $etape = new Etape();
                $etape->setContent($node->text());
                $etape->setStep($index + 1);
                $etape->setRecette($recette);
                return $etape;
            });

            // Persist Recette, Ingredient, and Etape entities
            $this->entityManager->persist($recette);
            foreach ($ingredientItems as $ingredient) {
                $this->entityManager->persist($ingredient);
            }
            foreach ($stepsItems as $etape) {
                $this->entityManager->persist($etape);
            }
            $this->entityManager->flush();
        }

        $output->writeln('Recipes have been successfully scraped and saved.');

        return Command::SUCCESS;
    }
}
