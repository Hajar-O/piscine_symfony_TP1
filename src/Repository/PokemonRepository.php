<?php

namespace App\Repository;

use App\Entity\Pokemon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pokemon>
 */
class PokemonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pokemon::class);
    }

    public function findLikeTitle($search)
    {
        //création de la requete SQL par la methode createQueryBuilder.
        $queryBuilder = $this->createQueryBuilder('pokemon');

        $query = $queryBuilder->select('pokemon')
            //On sécuriste avec :search pour éviter les injections SQL.
            ->where('pokemon.title LIKE :search')
            //On programme les paramètres de recherche.
            ->setParameter('search', '%'.$search.'%')
            //On exécute la requêtre.
            ->getQuery();
        // On recupère les données de la requête.
        $pokemons = $query->getArrayResult();
        // On retourne la tableau.
        return $pokemons;
    }

    //    /**
    //     * @return Pokemon[] Returns an array of Pokemon objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Pokemon
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
