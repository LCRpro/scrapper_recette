<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Recette;
use App\Entity\Ingredient;
use App\Entity\Etape;

class ImportJson extends Command
{
    protected static $defaultName = 'app:import';
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this->setName('app:import')
            ->setDescription('Importer les données des recettes depuis un JSON vers la base de données');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Fichier JSON
        $jsonFile = 'recette.json';
        if (!file_exists($jsonFile)) {
            $output->writeln("Le fichier '$jsonFile' n'existe pas.");
            return Command::FAILURE;
        }

        $jsonData = file_get_contents($jsonFile);
        $recipes = json_decode($jsonData, true);

        // Traiter chaque recette dans le fichier JSON
        foreach ($recipes as $recipeData) {
            // Créer et sauvegarder une recette (Recette)
            $recette = new Recette();
            $recette->setTitle($recipeData['title']);

            // Persister l'entité Recette
            $this->entityManager->persist($recette);
            
            // Traiter et sauvegarder les ingrédients (Ingredient)
            foreach ($recipeData['ingredients'] as $ingredientContent) {
                $ingredient = new Ingredient();
                $ingredient->setContent($ingredientContent);
                $ingredient->setRecette($recette);
                $this->entityManager->persist($ingredient);
            }

            // Traiter et sauvegarder les étapes (Etape)
            foreach ($recipeData['steps'] as $index => $stepContent) {
                $etape = new Etape();
                $etape->setContent($stepContent);
                $etape->setStep($index + 1);
                $etape->setRecette($recette);
                $this->entityManager->persist($etape);
            }
        }

        // Envoyer toutes les entités persistées vers la base de données
        $this->entityManager->flush();

        $output->writeln('Les recettes ont été importées avec succès depuis JSON vers la base de données.');

        return Command::SUCCESS;
    }
}
