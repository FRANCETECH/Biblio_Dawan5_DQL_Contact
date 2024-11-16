<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Livre;
use App\Entity\User;

final class LivreVoter extends Voter
{
    // Est la liste des constantes de permission: représentent les actions que ce Voter peut contrôler.
    public const EDIT = 'LIVRE_EDIT';
    public const VIEW = 'LIVRE_VIEW';
    public const CREATE = 'LIVRE_CREATE';
    public const LIST = 'LIVRE_LIST';
    public const LIST_ALL = 'LIVRE_ALL';   // A garder pour l'explication *A* car il faut modifier également la fonction index dans LivreController pour la pagination


    // Cette méthode vérifie si l'attribut (action demandée) est supporté par ce Voter.
    protected function supports(string $attribute, mixed $subject): bool
    {

        // soit on n'a la permière condition, dans ce cas pas besoin de $subject || ou soit on n'a la deuxième condition avec le sujet $subject 
        // Elle retourne true si l'action fait partie des permissions définies (comme LIVRE_CREATE, LIVRE_LIST, etc.).
        return in_array($attribute, [self::CREATE, self::LIST , self::LIST_ALL]) ||
            (
                // La méthode vérifie également si l'attribut EDIT ou VIEW est demandé, et si le sujet de la demande est bien une instance de Livre.
                in_array($attribute, [self::EDIT, self::VIEW])
                //&& $subject instanceof \App\Entity\Livre
                && $subject instanceof Livre
            );

    }


    /**
     * Cette méthode détermine si l'utilisateur a la permission pour l'action demandée.
     * @param Livre|null $subject
     */

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser(); // On commence par récupérer l'utilisateur actuel depuis le TokenInterface.

        // Si l'utilisateur n'est pas un utilisateur authentifié, je refuse l'accès en retournant false
        if (!$user instanceof User) {
            // Si l'utilisateur n'est pas connecté (User est anonyme ou inexistant), l'accès est refusé (return false).
            return false;
        }

        // Le switch vérifie quelle action (attribut) est demandée et décide de la permission
        switch ($attribute) {
            //Pour l'action EDIT, on vérifie si l'utilisateur connecté est bien celui qui a créé la recette
            case self::EDIT:
                // Permet de verifier que l'utilisateur connecté est bien celui qui a créer la recette (basé sur l'ID utilisateur)
                return $subject->getUser()->getId() === $user->getId();

            // Pour les autres actions (LIST, CREATE, VIEW), l'accès est toujours autorisé (simple return true).
            case self::LIST:    //Tout le monde a desormais le droit de lister, créer, voir si on n'est connecté. 
            case self::CREATE:
            case self::VIEW:
                return true;


        }

        return false;
    }
}
