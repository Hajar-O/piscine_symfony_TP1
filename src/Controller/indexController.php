<?php
//création d'un namespace (le chemin) qui indique l'emplacement des class.
namespace App\Controller;

// On appelle le namespace des class utilisées afin que Symfony fasse le require de ces dernières.
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

//Le nom de la class ===  nom fichier (à la majuscule près).
//On étand la class AbstractController qui permet d'utiliser les fontions utilitaires pour les controllers.
class indexController extends AbstractController
{
//annotation précédé par un #. Permet d'enrichir une fonctionnalité mais qui n'est pas vraiment du code.
// création d'une nouvelle page avec le chemain et le nom.
    #[Route('/', name: 'home')]

    public function index(){
         return $this->render('page/index.html.twig');

    }
}
