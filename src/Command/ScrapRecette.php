<?php 
namespace App\Command;

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

class ScrapRecette extends Command
{
    protected static $defaultName = 'app:recette';
    
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('app:recette')
            ->setDescription('Récupère les recettes à partir d\'une URL et enregistre les données au format JSON');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Initialisation du client HTTP
        $client = new HttpBrowser(HttpClient::create([
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3'
            ]
        ]));

        // Récupération du contenu HTML depuis l'URL spécifiée
        $crawler = $client->request('GET', 'https://www.allrecipes.com/recipes/77/drinks/');
        // Filtrage des liens vers les recettes
        $links = $crawler->filter('div.mntl-taxonomysc-article-list-group a.mntl-card-list-items')->links();

        // Tableau pour stocker les recettes
        $recipes = [];

        // Parcours des liens des recettes
        foreach ($links as $link) {
            $url = $link->getUri();
            // Accès à chaque page de recette
            $crawler = $client->request('GET', $url);

            // Extraction du titre de la recette
            $title = $crawler->filter('h1')->text();

            // Extraction des ingrédients de la recette
            $ingredients = $crawler->filter('ul.mm-recipes-structured-ingredients__list li')->each(function (Crawler $node) {
                return $node->text();
            });

            // Extraction des étapes de la recette
            $steps = $crawler->filter('div.mm-recipes-steps ol li')->each(function (Crawler $node) {
                return $node->text();
            });

            // Ajout des données de la recette au tableau des recettes
            $recipes[] = [
                'title' => $title,
                'ingredients' => $ingredients,
                'steps' => $steps
            ];
        }

        // Conversion du tableau des recettes en format JSON
        $json = json_encode($recipes, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        // Enregistrement du JSON dans un fichier
        file_put_contents('recette.json', $json);

        // Affichage d'un message de confirmation
        $output->writeln('Les recettes ont été récupérées avec succès et enregistrées dans recette.json.');

        return Command::SUCCESS;
    }
}
