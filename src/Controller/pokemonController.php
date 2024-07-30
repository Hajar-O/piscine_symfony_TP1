<?php
// On indique à PHP qu’on va respecter strictement le typage.
declare(strict_types=1);
//création d'un namespace (le chemin) qui indique l'emplacement des class.
namespace App\Controller;

// On appelle le namespace des class utilisées afin que Symfony fasse le require de ces dernières.

use App\Entity\Pokemon;
use App\Form\PokemonBuilderType;
use App\Repository\PokemonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
class pokemonController extends AbstractController {

#[Route('/show-pokemon', name: 'show_pokemon')]

private array $pokemons;

    function __construct()
    {
        $this->pokemons = [
            [
                'id' => 1,
                'title' => 'Carapuce',
                'content' => 'Pokemon eau',
                'isPublished' => true,
                'img' => 'img/carapuce.png'
            ],
            [
                'id' => 2,
                'title' => 'Salamèche',
                'content' => 'Pokemon feu',
                'isPublished' => true,
                'img' => 'img/salameche.png'
            ],
            [
                'id' => 3,
                'title' => 'Bulbizarre',
                'content' => 'Pokemon plante',
                'isPublished' => true,
                'img' => 'img/Bulbizarre.png'
            ],
            [
                'id' => 4,
                'title' => 'Pikachu',
                'content' => 'Pokemon electrique',
                'isPublished' => true,
                'img' => 'img/pikachu.png'
            ],
            [
                'id' => 5,
                'title' => 'Rattata',
                'content' => 'Pokemon normal',
                'isPublished' => false,
                'img' => 'carapuce.png'
            ],
            [
                'id' => 6,
                'title' => 'Roucool',
                'content' => 'Pokemon vol',
                'isPublished' => true,
                'img' => 'img/roucoul.png'
            ],
            [
                'id' => 7,
                'title' => 'Aspicot',
                'content' => 'Pokemon insecte',
                'isPublished' => false,
                'img' => 'carapuce.png'
            ],
            [
                'id' => 8,
                'title' => 'Nosferapti',
                'content' => 'Pokemon poison',
                'isPublished' => false,
                'img' => 'carapuce.png'
            ],
            [
                'id' => 9,
                'title' => 'Mewtwo',
                'content' => 'Pokemon psy',
                'isPublished' => true,
                'img' => 'img/mewtwo.png'
            ],
            [
                'id' => 10,
                'title' => 'Ronflex',
                'content' => 'Pokemon normal',
                'isPublished' => false,
                'img' => 'carapuce.png'
            ]

        ];
    }
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

    #[Route('/articles', name: 'list_articles')]
    public function listArticles(){



        return $this->render('page/articles.html.twig', [
            'pokemons' => $this->pokemons
        ]);

    }

    #[Route('/show-pokemon/{id}', name: 'show_pokemon')]

    public function showPokemon($id): Response
    {

        //$request = Request::createFromGlobals();
       // $id = $request->query->get('id');

        $pokemonFound = null;

        foreach ($this->pokemons as $pokemon) {
            if($pokemon['id'] === (int)$id) {
                $pokemonFound = $pokemon;
            }
        }

        return $this->render('page/oneArticle.html.twig', [
            'pokemon' => $pokemonFound
        ]);
    }

#[Route('/pokemon-bdd', name: 'pokemon_bdd')]
public function showPokemonBdd(PokemonRepository $pokemonRepository): Response{

        $pokemons= $pokemonRepository->findAll();

    return $this->render('page/list_pokemon_bdd.html.twig', [
        'pokemons' => $pokemons
        ]);

}
#[Route('/show-pokemon-bdd/{id}', name: 'show_pokemon_bdd')]
public function showPokemonById(int$id,PokemonRepository $pokemonRepository ): Response{
        $pokemon = $pokemonRepository->find($id);

        return $this->render('page/pokemonShowId.html.twig', [
            'pokemon' => $pokemon
        ]);
}

#[Route('/found-pokemon-bdd', name: 'found_pokemon_bdd')]
public function showFoundPokemonBdd(Request $request,PokemonRepository $pokemonRepository ): Response{
        $pokemonFound= [];
    if($request->request->has('title')){
        // je recupère les données post du title dans search
            $search = $request->request->get('title');

        //je stock dans la variable $pokemonFound la recherche de l'utiliateur si Pokemon existant dans la bdd.
            $pokemonFound = $pokemonRepository->findOneBy(['title' => $search]);
        // si pas de pokemon dans $pokemonFound alors je génère une page 404 à la main.

            if(!$pokemonFound){
                $html = $this->renderView('page/pokemon_not_found.html.twig');
                    return new Response($html,404);
             }

    }

       return $this->render('page/search_pokemon.html.twig', [
            'pokemon' => $pokemonFound
        ]);
}
    #[Route('/found-like-pokemon-bdd', name: 'found_like_pokemon_bdd')]
    public function findLikeTitle(Request $request,PokemonRepository $pokemonRepository ): Response{
        $pokemonsFound=null;
        if($request->request->has('title')){
            // je recupère les donnérs poste du title dans search
            $search = $request->request->get('title');

            //je stock dans la variable $pokemonFound la recherche de l'utiliateur si Pokemon existant dans la bdd.
            $pokemonsFound = $pokemonRepository->findLikeTitle($search);
            // si pas de pokemon dans $pokemonFound alors je génère une page 404 à la main.

            if(!$pokemonsFound){
                $html = $this->renderView('page/pokemon_not_found.html.twig');
                return new Response($html,404);
            }

        }

        return $this->render('page/found_like_pokemon_bdd.html.twig', [
            'pokemons' => $pokemonsFound
        ]);
    }

        #[Route('/delete-pokemon-bdd/{id}', name: 'delete_pokemon_bdd')]
        public function deletePokemon(int $id,PokemonRepository $pokemonRepository, EntityManagerInterface $entityManager):Response
        {
        $pokemon = $pokemonRepository->find($id);
        if($pokemon === null){
            $html = $this->renderView('page/pokemon_not_found.html.twig');
            return new Response($html,404);
        }

        //j'utilise la classe entity manager
        //pour préparer la requête SQL de suppression
        //cette requête n'est pas executé tout de suite

        $entityManager->remove($pokemon);
        //flush -> exécute la requête
        $entityManager->flush();

        return $this->redirectToRoute('pokemon_bdd');
    }

    #[Route('/insert-pokemon-bdd', name: 'insert_pokemon_bdd')]
    public function insertPokemon(EntityManagerInterface $entityManager, Request $request){


        // J'initialise la variable $pokemon à null
        // pour l'utiliser en dehors de la condition

        $pokemon = null;
        // si le method est du post
        if($request->getMethod()=== 'POST') {

            // je récupère les données du formulaire

            $title= $request->request->get('title');
            $description = $request->request->get('description');
            $type = $request->request->get('type');
            $image = $request->request->get('image');

            //j'instencie la class pokemon

            $pokemon = new Pokemon();

            // Je passe en paramètre les valeurs
            $pokemon->setTitle($title);
            $pokemon->setDescription( $description);
            $pokemon->setImage($image);
            $pokemon->setType($type);

            //j'enregistre les valeurs dans la bdd
            $entityManager->persist($pokemon);
            $entityManager->flush();
        }

        // je retourne un réponse.
        return $this->render('page/insert_pokemon_without_form.html.twig', [
         'pokemon' => $pokemon
     ]);
}

    #[Route('/insert-pokemon-builder', name: 'insert_pokemon_builder')]
    public function insertFromBuilder(Request $request, EntityManagerInterface $entityManager){
    // on a créé une classe de "gabarit de formulaire HTML" avec php bin/console make:form

    // je créé une instance de la classe d'entité Pokemon
    $pokemon = new Pokemon();

    // permet de générer une instance de la classe de gabarit de formulaire
    // et de la lier avec l'instance de l'entité
    $pokemonForm = $this->createForm(PokemonBuilderType::class, $pokemon);

    // lie le formulaire avec la requête
    $pokemonForm->handleRequest($request);


    // si le formulaire a été envoyé et que ces données
    // sont correctes
    if ($pokemonForm->isSubmitted() && $pokemonForm->isValid()) {
        $entityManager->persist($pokemon);
        $entityManager->flush();
    }

    return $this->render('page/insert_from_builder.html.twig', [
        'pokemonForm' => $pokemonForm->createView()
    ]);
}

    
}
