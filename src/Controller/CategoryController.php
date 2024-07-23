<?php
//création d'un namespace (le chemin) qui indique l'emplacement des class.
namespace App\Controller;

// On appelle le namespace des class utilisées afin que Symfony fasse le require de ces dernières.
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

//Le nom de la class ===  nom fichier (à la majuscule près).
//On étand la class AbstractController qui permet d'utiliser les fontions utilitaires pour les controllers.
class CategoryController extends AbstractController{

    #[Route('/categories', name: 'categories')]

public function listCategories(){

        $categories = [
            'Red', 'Green', 'Blue', 'Yellow', 'Gold', 'Silver', 'Crystal'
        ];
//J'utilise la méthode renderView, en lui passant en paramère la view et la variable dans laquel est stocké le tableau.
        $html = $this->renderView('page/category.html.twig', [
            'categories' => $categories
        ]);
// nouvelle instense de Response en passant en paramètre la variable où est stocké le Render et le code URL 200.
            return new Response ($html,200);

    }
}
