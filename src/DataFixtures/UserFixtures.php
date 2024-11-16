<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture // Anciennement AppFixtures
{
    public const ADMIN = 'ADMIN_USER'; 
    public function __construct( 
        private readonly UserPasswordHasherInterface $hasher
    ){
        /* Rôle du readonly
        L'utilisation de readonly présente plusieurs avantages :-Immutabilité: Assure que la propriété ne change pas après son initialisation, ce qui 
        peut améliorer la prévisibilité du comportement de l'objet.-Sécurité: Empêche les modifications accidentelles ou malveillantes de la propriété 
        après l'initialisation, augmentant ainsi la sécurité de l'application.-Clarté: Clarifie l'intention du développeur que cette propriété est 
        censée rester constante tout au long de la vie de l'objet.
        Cela garantit que l'instance de UserPasswordHasherInterface utilisée pour hacher les mots de passe reste la même pour toute la durée de vie de 
        l'objet UserService, assurant ainsi une consistance et une sécurité accrues. 
        */
        
    }


     // Permet de créer plusieurs utilisateurs (1)
    public function load(ObjectManager $manager): void  


    {
        $user = (new User());   //(2)
        $user->setRoles(['ROLE_ADMIN'])  // Je le remplis avec des informations
            ->setEmail('admin@dawan.fr')
            ->setUsername('admin')
            ->setVerified(true)
            ->setPassword($this->hasher->hashPassword($user, 'admin')); //(3')Nous avons besoin du hasher pour la partie mdp, pour ça->(le 3 injection de dep)
          
        $this->addReference(self::ADMIN, $user);  // (4)

        $manager->persist($user); // (Niveau1) // On lui démande de persister car on n'a le manager

        for ($i = 1; $i <= 10; $i++) {
            $user = (new User())
                ->setRoles([])
                ->setEmail("user{$i}@doe.fr")
                ->setUsername("user{$i}")
                ->setVerified(true)
                ->setPassword($this->hasher->hashPassword($user, '0000'));
               
            $this->addReference('USER' . $i, $user); 

            $manager->persist($user);
        }


        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();   // va avec le (Niveau1)
        //  ->setVerified(true)
    }
}
