<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
class pokemonController extends AbstractController {

#[Route('/show-pokemon', name: 'show_pokemon')]

     public function showPokemon(){
    $pokemons = [
        [
            'id' => 1,
            'title' => 'Carapuce',
            'content' => 'Pokemon eau',
            'isPublished' => true,
            'img' => 'carapuce.png'
        ],
        [
            'id' => 2,
            'title' => 'Salamèche',
            'content' => 'Pokemon feu',
            'isPublished' => true,
            'img' => 'salameche.png'
        ],
        [
            'id' => 3,
            'title' => 'Bulbizarre',
            'content' => 'Pokemon plante',
            'isPublished' => true,
            'img' => 'carapuce.png'
        ],
        [
            'id' => 4,
            'title' => 'Pikachu',
            'content' => 'Pokemon electrique',
            'isPublished' => true,
            'img' => 'carapuce.png'
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
            'img' => 'carapuce.png'
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
            'img' => 'carapuce.png'
        ],
        [
            'id' => 10,
            'title' => 'Ronflex',
            'content' => 'Pokemon normal',
            'isPublished' => false,
            'img' => 'carapuce.png'
        ]

    ];
    $request = Request::createFromGlobals();
    $id = $request->query->get('id');

        $pokemonFound = null;

        foreach ($pokemons as $pokemon) {
            if($pokemon['id'] === (int)$id) {
                $pokemonFound = $pokemon;
            }
        }

        return $this->render('page/oneArticle.html.twig', [
            'pokemon' => $pokemonFound
        ]);
             }

}
