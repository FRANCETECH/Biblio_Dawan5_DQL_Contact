<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

// Les anotations trouvées étaient liées à l'encienne notation, ce système d'anotation n'etant plus utilisé

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class BanWord extends Constraint // Ainsi je peux utiliser desormain Banword dans l'entité Recipe
{

    // On defini notre propre constructeur, donc personnalisé
    public function __construct(
        // est le message à afficher si on cherche à soumettre un mot bani, {{ banWord }}: a été defini dans BanWordValidator
        public string $message = 'This contains a banned word "{{ banWord }}".',
        public array  $banWords = ['spam', 'viagra'], // est ma liste de mots banis
        ?array $groups = null, //
        mixed $payload = null) //
    {
        parent::__construct(null, $groups, $payload); // On rajoute cette ligne pour lui dire d'appeler le constructeur parent de la class Constraint
    }
}


// https://chatgpt.com/c/66f2d5ae-4e34-800a-a0fc-5029ab02b73f
