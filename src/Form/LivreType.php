<?php

namespace App\Form;

use App\Entity\Livre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Category;

class LivreType extends AbstractType
{
    public function __construct(private FormListenerFactory $listenerFactory)
    {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                // Cette option définit ce que Symfony va attribuer comme valeur au champ si l'utilisateur ne remplit pas ce champ. on aura une chaine vide aulieu de null
                'empty_data' => ''
            ])
            ->add('slug', TextType::class, [
                'required' => false
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                //'expanded' => true,
                'choice_label' => 'name',
            ])
            ->add('author', TextType::class, [
                'required' => false
            ])
            ->add('publicationYear', IntegerType::class, [
                'empty_data' => 0 // Vous pouvez utiliser une autre valeur par défaut si nécessaire, comme -1 ou une autre valeur spécifique
            ])
            /*
            ->add('createdAt', null, [
                'widget' => 'single_text',
                'empty_data' => (new \DateTime())->format('Y-m-d') // Par défaut, vous pouvez mettre la date actuelle ou toute autre valeur
            ])
            ->add('updatedAt', null, [
                'widget' => 'single_text',
                'empty_data' => (new \DateTime())->format('Y-m-d') // Idem pour une date par défaut
            ])
            */
            ->add('genre', TextType::class, [
                'empty_data' => '' // Chaîne vide si aucune donnée n'est entrée
            ])
            ->add('summary', TextType::class, [
                'empty_data' => '' // Chaîne vide si aucune donnée n'est entrée
            ])
            ->add('publisher', TextType::class, [
                'empty_data' => '' // Chaîne vide si aucune donnée n'est entrée
            ])
            ->add('language', TextType::class, [
                'empty_data' => '' // Chaîne vide si aucune donnée n'est entrée
            ])
            ->add('edition', TextType::class, [
                'empty_data' => '' // Chaîne vide si aucune donnée n'est entrée
            ])
            ->add('coverImage', TextType::class, [
                'required' => false, // Rend le champ facultatif // Si cette ligne est absente il nous impose de remplir le champ avant de valider le formulaire
                'empty_data' => '' // Cela reste une option pour définir une chaîne vide par défaut si nécessaire
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Envoyer'
            ])

            ->addEventListener(FormEvents::PRE_SUBMIT, $this->listenerFactory->autoslug('title'))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->listenerFactory->timestamps())



        ;
    }



     // La méthode configureOptions est utilisée pour configurer les options du formulaire.
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // l'option par défaut data_class est définie sur la classe Recipe, indiquant que le formulaire doit être lié à des objets de cette classe, ici la class Recipe.
            'data_class' => Livre::class,
        ]);
    }
}




// https://chatgpt.com/c/67336566-a2d8-800a-9f73-db25477e0f81
