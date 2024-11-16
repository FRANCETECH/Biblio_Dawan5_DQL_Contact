<?php

namespace App\Repository;

use App\Entity\Livre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;



/**
 * @extends ServiceEntityRepository<Livre>
 */
class LivreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, livre::class);
    }




    public function paginatelivres(int $page, ?int $userId): PaginationInterface // On replace le type de retour par PaginationInterface qui est iterable
    {

        // Construction de la requête DQL de base pour récupérer les livres (Livre) et leurs catégories associées (Category)
        // Utilisation d'une jointure LEFT JOIN pour inclure la catégorie même si elle est null
        $dql = 'SELECT l, c FROM App\Entity\Livre l LEFT JOIN l.category c';

        // Si un userId est fourni (différent de null), ajouter une clause WHERE pour filtrer les livres de l'utilisateur spécifique
        if ($userId) {
            $dql .= ' WHERE l.user = :userId';
        }

        // Création de la requête en utilisant le DQL construit précédemment
        $query = $this->getEntityManager()->createQuery($dql);

        // Si un userId est fourni, définir le paramètre pour lier la valeur du :userId dans la requête
        if ($userId) {
            $query->setParameter('userId', $userId);
        }

        // Utilisation du paginator pour paginer les résultats
        // Le paginator divise les résultats en pages, ici avec une limite de 5 résultats par page
        return $this->paginator->paginate(
            $query, // La requête DQL à paginer
            $page, // Le numéro de la page à afficher
            5, // Le nombre de résultats par page
            [
                'distinct' => false, // Indique si les résultats doivent être distincts (ici, faux pour autoriser les doublons si nécessaires) //  ATTENTION 
                'sortFieldAllowList' => ['l.id', 'l.title'] // Définit les champs de tri autorisés pour la pagination
            ]
        );


        /*
        
        // Création d'un QueryBuilder pour récupérer les recettes (alias 'r') et les catégories associées (alias 'c').
        // La méthode 'leftJoin' est utilisée pour inclure les catégories (même s'il n'y a pas de correspondance).
        $builder = $this->createQueryBuilder('l')->leftJoin('l.category', 'c')->select('l', 'c'); // Jointure avec la table 'category' liée à 'r' (recettes)
        // Sélection des colonnes de 'r' et 'c'.
        if($userId) {     // Si $userId est fourni, on ajoute une condition pour filtrer les recettes par utilisateur.                                                                                                  
            $builder = $builder->andWhere('l.user = :user') // Condition pour vérifier l'appartenance des recettes à l'utilisateur donné.
                ->setParameter('user', $userId);    // Attribution de la valeur de $userId au paramètre :user.
        }

        // Utilisation du paginator pour renvoyer une pagination des résultats.
        return $this->paginator->paginate(
            $builder, // Le query builder configuré ci-dessus.
            $page,      // La page actuelle à afficher.
            5,         // Nombre d'éléments par page.
            [
                'distinct' => false,    // Pas de distinction stricte entre les résultats, permet d'éviter des doublons.
                'sortFieldAllowList' => ['l.id', 'l.title'] // Champs de tri autorisés : 'id' et 'title' des recettes.
            ]
            );

        */



    }



    /**
     * Calcule et retourne la somme totale de toutes les valeurs du champ publicationYear de l'entité.
     *
     * @return int La somme totale des années de publication.
     */
    public function findTotalYear(): int
    {
        // Création d'un QueryBuilder pour construire une requête sur l'entité associée au repository, avec l'alias 'l'.
        return $this->createQueryBuilder('l')
            // Sélectionne la somme de la colonne publicationYear et l'alias 'total' pour le résultat.
            ->select('SUM(l.publicationYear) as total')
            // Exécute la requête et récupère le résultat en tant que valeur scalaire unique (un entier dans ce cas).
            ->getQuery()
            ->getSingleScalarResult();
    }



    /**
     * Trouve et retourne les enregistrements dont l'année de publication est inférieure ou égale à une valeur donnée.
     *
     * @param int $publicationYear L'année de publication maximale à comparer.
     * @return array Un tableau contenant les résultats correspondant aux critères de la requête.
     */
    public function findWithPublicationYearLowerThan(int $publicationYear): array
    {
        // Création d'un QueryBuilder pour construire une requête sur l'entité associée au repository.
        return $this->createQueryBuilder('l')
            // Ajoute une condition "WHERE" pour sélectionner les enregistrements dont l'année de publication
            // est inférieure ou égale à la valeur fournie en paramètre.
            ->where('l.publicationYear <= :publicationYear')
            // Trie les résultats par année de publication en ordre croissant (ASC).
            ->orderBy('l.publicationYear', 'ASC')
            // Limite les résultats de la requête à un maximum de 10 enregistrements.
            ->setMaxResults(10)
            // Définit la valeur du paramètre ":publicationYear" utilisé dans la clause "WHERE".
            ->setParameter('publicationYear', $publicationYear)
            // Exécute la requête et récupère les résultats sous forme d'un tableau d'objets.
            ->getQuery()
            ->getResult();
    }



























    //    /**
//     * @return Livre[] Returns an array of Livre objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    //    public function findOneBySomeField($value): ?Livre
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
