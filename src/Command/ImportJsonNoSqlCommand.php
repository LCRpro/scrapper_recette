<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\Recette;
use App\Document\Ingredient;
use App\Document\Etape;

class ImportJsonNoSqlCommand extends Command
{
    protected static $defaultName = 'app:import-nosql';
    private $documentManager;

    public function __construct(DocumentManager $documentManager)
    {
        parent::__construct();
        $this->documentManager = $documentManager;
    }

    protected function configure()
    {
        $this
            ->setName('app:import-nosql')
            ->setDescription('Importer les données des recettes depuis un JSON vers la base de données NoSQL');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $jsonFile = 'recettes.json';
        if (!file_exists($jsonFile)) {
            $output->writeln("Le fichier '$jsonFile' n'existe pas.");
            return Command::FAILURE;
        }

        $jsonData = file_get_contents($jsonFile);
        $recipes = json_decode($jsonData, true);

        foreach ($recipes as $recipeData) {
            $recette = new Recette();
            $recette->setTitle($recipeData['title']);
            $this->documentManager->persist($recette);

            foreach ($recipeData['ingredients'] as $ingredientContent) {
                $ingredient = new Ingredient();
                $ingredient->setContent($ingredientContent);
                $ingredient->setRecette($recette);
                $this->documentManager->persist($ingredient);
            }

            foreach ($recipeData['steps'] as $index => $stepContent) {
                $etape = new Etape();
                $etape->setContent($stepContent);
                $etape->setStep($index + 1);
                $etape->setRecette($recette);
                $this->documentManager->persist($etape);
            }
        }

        $this->documentManager->flush();

        $output->writeln('Les recettes ont été importées avec succès depuis JSON vers la base de données NoSQL.');

        return Command::SUCCESS;
    }
}
