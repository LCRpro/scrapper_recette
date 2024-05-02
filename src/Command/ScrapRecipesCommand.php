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
        // Explicitly set the command name here
        $this->setName('app:recette')
             ->setDescription('Scrap recipes from a URL and insert data into database');
    }   

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $client = new HttpBrowser(HttpClient::create());
        $crawler = $client->request('GET', 'https://www.allrecipes.com/recipes/77/drinks/');

        $recipeLinks = $crawler->filter('a.mntl-card-list-items')->links();

        foreach ($recipeLinks as $link) {
            $crawler = $client->click($link);

            // Extract title
            $title = $crawler->filter('h1.recipe-title')->text();
            
            // Create Recette entity
            $recette = new Recette();
            $recette->setTitle($title);

            // Extract and save ingredients
            $ingredientItems = $crawler->filter('ul.mntl-structured-ingredients__list li')->each(function (Crawler $node) use ($recette) {
                $ingredient = new Ingredient();
                $ingredient->setContent($node->text());
                $ingredient->setRecette($recette);
                return $ingredient;
            });

            // Extract and save steps
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
