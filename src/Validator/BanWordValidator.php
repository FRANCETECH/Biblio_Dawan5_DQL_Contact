<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class BanWordValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint) // Constraint à la place de Banword, et à la ligne 12 on n'a utilisé PHP Doc pour commenter
    {
        /* @var BanWord $constraint */

        if (null === $value || '' === $value) { // si la valeur est null ou vide, on return, on considère qu'elle est valide
            return;
        }

        $value = strtolower($value); // Permet de mettre la valeur en minuscule
        foreach ($constraint->banWords as $banWord) { // Je boucle sur l'ensemble des mots banis
            if (str_contains($value, $banWord)) { // Si la chaine de caractère value contient le mot bani
                $this->context->buildViolation($constraint->message)  // Dans ce cas on n'a une erreur. Code defini lors de la création de cette class
                    ->setParameter('{{ banWord }}', $banWord) // ici value a été remplacé par banword et $value par $banword
                    ->addViolation();
            }
        }

    }
}
