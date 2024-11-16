<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>  // Modif
 *
 * @implements PasswordUpgraderInterface<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function findUserByEmailOrUsername(string $usernameOrEmail): ?User
    {
        // La requête DQL qui sélectionne l'utilisateur dont l'email ou le nom d'utilisateur 
        // correspond à l'identifiant fourni
        $dql = 'SELECT u FROM App\Entity\User u WHERE u.email = :identifier OR u.username = :identifier';

        // Exécution de la requête avec l'EntityManager, on passe l'email ou le nom d'utilisateur
        // comme paramètre. On limite le résultat à 1 en cas de multiples correspondances.
        return $this->getEntityManager()            // Récupère l'EntityManager pour créer et exécuter des requêtes
            ->createQuery($dql)             // Crée une requête à partir de la DQL spécifiée
            ->setParameter('identifier', $usernameOrEmail)  // Définit la valeur du paramètre ":identifier" 
            ->setMaxResults(1)              // Limite le nombre de résultats à 1
            ->getOneOrNullResult();         // Retourne l'utilisateur trouvé ou null si aucun résultat


        /*
        return $this->createQueryBuilder('u') // alias u comme utilisateur à chercher avec le where
            ->where('u.email = :identifier OR u.username = :identifier')  // identifier = ce qui a été demandé
            ->setParameter('identifier', $usernameOrEmail)                // le param identifier = ce qui est passé en paramètre
            ->setMaxResults(1)                                            // 1 car j'attends 1 seul enregistrement
            ->getQuery()  // On lui demande de generer la requete
            ->getOneOrNullResult(); //Permet d'obtenir un resultat ou null
        */
    }







    //    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    //    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
