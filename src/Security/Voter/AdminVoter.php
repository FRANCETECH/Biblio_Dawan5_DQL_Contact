<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;


// Ce voter est utilisé pour déterminer si un utilisateur a le droit de réaliser certaines actions.
final class AdminVoter extends Voter{


    // Méthode pour vérifier si ce voter supporte l'attribut (action) et le sujet (l'entité concernée).
    // Ici, cette méthode retourne toujours `true`, ce qui signifie que ce voter sera exécuté pour toute demande.
    protected function supports(string $attribute, mixed $subject): bool
    {
        return true; // Ce voter accepte de gérer toutes les actions, quel que soit le sujet.
    }

   
    // Méthode principale qui décide si l'utilisateur peut réaliser l'action demandée sur le sujet donné.
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        // Récupération de l'utilisateur à partir du token de sécurité.
        $user = $token->getUser();
        // Si l'utilisateur est anonyme (non authentifié), il n'a pas les droits d'accès.
        if (!$user instanceof UserInterface) {
            return false; // Refus d'accès si l'utilisateur n'est pas identifié.
        }

        // Vérifie si l'utilisateur possède le rôle d'administrateur ('ROLE_ADMIN').
        // Si c'est le cas, l'accès est autorisé, sinon il est refusé.
        return in_array('ROLE_ADMIN', $user->getRoles());

    }
}
