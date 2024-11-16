<?php

namespace App\Controller\Admin;

use App\Entity\Livre;
use App\Form\LivreType;
use App\Security\Voter\LivreVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\SecurityBundle\Security;


#[Route('admin/livre', name: 'admin.livre.')]
//#[IsGranted('ROLE_ADMIN')]
class LivreController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(LivreRepository $repository, Request $request, Security $security): Response
    {
        // Vérifier si l'utilisateur est connecté
        $user = $security->getUser();
        if (!$user) {
            // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
            return $this->redirectToRoute('app_login');
        }

        $page = $request->query->getInt('page', 1);
        $userId = $security->getUser()->getId();   // Récupération de l'ID de l'utilisateur connecté
        // En utilisant la cste VIEW: On verra que ces recettes et on peut les modifier ou supprimer, on peut créer aussi
        $canListAll = $security->isGranted(LivreVoter::LIST_ALL);   // Vérifier si l'utilisateur a la permission de lister tous les livres
        $livres = $repository->paginatelivres($page, $canListAll ? null : $userId);

        return $this->render('admin/livre/index.html.twig', [
            'livres' => $livres
        ]);
    }


    // Cette show est la fonction index() au debut   //requirements: format attendu  
    // 'slug' => '[a-z09-]+'] : erreur pour les majuscule comme: https://127.0.0.1:8000/livre/picasso-29
    #[Route('/{slug}-{id}', name: 'show', requirements: ['id' => '\d+', 'slug' => "[A-Za-z0-9-'éàùç]*"])]
    public function show(Request $request, string $slug, int $id, LivreRepository $repository): Response
    {
        //dd($request); // (1)
        // https://localhost:8000/livre/symfony-7  //  (2)
        //dd($request->attributes->get('slug'), $request->attributes->get('id'));// Permet de recuperer ces attributs. On utiliser getInt à la place de get

        /* A faire une fois la BDD créee avec les 3 livres */
        $livre = $repository->find($id);
        if ($livre->getSlug() !== $slug) {
            return $this->redirectToRoute('admin.livre.show', ['slug' => $livre->getSlug(), 'id' => $livre->getId()]);
        }

        return $this->render('admin/livre/show.html.twig', [
            'livre' => $livre
        ]);
    }


    // Partie à faire au chapitre formulaire
    // J'utilise id pour retrouver livre / Ou simplement pour recuperer une recette
    //  L'EntityManagerInterface est utilisée pour gérer les entités dans la base de données. Cela inclut la création, la lecture, la mise à jour et la suppression (CRUD) des entités.
    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted(LivreVoter::EDIT, subject: 'livre')]
    public function edit(Livre $livre, Request $request, EntityManagerInterface $em)
    {
        //dd($livre);
        // Crée et renvoie une instance Form à partir du type du formulaire. Premier parametre: le formulaire qu'on souhaite utilisé,
        $form = $this->createForm(LivreType::class, $livre);  //second parametre:les données, ici l'entité pré rempli avec les données provenat de la BDD
        $form->handleRequest($request);  // Je demande à mon formulaire de gérer la requête, ce qui me permettra d'envoyer les donner à la fin
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();   // je fais appelle à mon EntityManagerInterface, et je peux lui dire flush (sauvegarde) mes données en tenant compte du changement.
            /*message à afficher à l'utilisateur. En parametre, on n'a le type et le message. On consomme le message sur twig grace notre objet global 
            app, voir dans base.html.twig*/
            $this->addFlash('success', 'Le livre a bien été modifié');
            return $this->redirectToRoute('admin.livre.index');
        }

        return $this->render('admin/livre/edit.html.twig', [
            'form' => $form,
            'livre' => $livre
        ]);
    }

    #[Route('/create', name: 'create')]
    #[IsGranted(LivreVoter::CREATE)]    // lui ne reçoit pas de sujet
    public function create(Request $request, EntityManagerInterface $em)
    {
        $livre = new livre();
        $form = $this->createForm(livreType::class, $livre);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($livre);
            $em->flush();

            $this->addFlash('success', 'La recette a bien été créee');
            return $this->redirectToRoute('admin.livre.index');
        }

        return $this->render('admin/livre/create.html.twig', [
            'form' => $form
        ]);

    }


    #[Route('/{id}/delete', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted(LivreVoter::EDIT, subject: 'livre')]
    public function remove(livre $livre, EntityManagerInterface $em)
    {
        $em->remove($livre);
        $em->flush(); // Sauvegarde les modifications au niveau de la BDD
        $this->addFlash('success', 'La recette a bien été supprimée');
        return $this->redirectToRoute('livre.index');
    }









}
