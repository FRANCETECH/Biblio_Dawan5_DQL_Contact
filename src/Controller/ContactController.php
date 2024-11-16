<?php 
// src/Controller/ContactController.php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]  // La route est définie ici
    public function index(Request $request, MailerInterface $mailer): Response
    {
        // Créez un formulaire basé sur le formulaire ContactType
        $form = $this->createForm(ContactType::class);

        // Traitez le formulaire lorsque l'utilisateur soumet les données
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire
            $data = $form->getData();

            // Créez l'email
            $email = (new Email())
                ->from('noreply@tonentreprise.com') // Adresse de l'expéditeur
                ->to('dev.technologie2018@gmail.com') // Destinataire
                ->subject('Nouveau message de contact') // Sujet de l'email
                ->html('<p><strong>Nom: </strong>' . $data['nom'] . '</p>
                        <p><strong>Prénom: </strong>' . $data['prenom'] . '</p> 
                        <p><strong>Email: </strong>' . $data['email'] . '</p> 
                        <p><strong>Email: </strong>' . $data['numero'] . '</p>
                        <p><strong>Message: </strong><br>' . nl2br($data['content']) . '</p>');

            // Envoi de l'email
            try {
                $mailer->send($email);
                // Ajout d'un message flash pour notifier l'utilisateur
                $this->addFlash('success', 'Votre message a été envoyé avec succès.');
            } catch (\Exception $e) {
                // Si l'envoi échoue, ajoutez un message d'erreur
                $this->addFlash('error', 'Erreur lors de l\'envoi de votre message. Veuillez réessayer.');
            }

            // Redirige vers la page de contact après soumission du formulaire
            return $this->redirectToRoute('app_contact');
        }

        // Si le formulaire n'est pas soumis ou invalide, affichez le formulaire
        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

// https://chatgpt.com/c/67375724-f7cc-800a-87d3-069566185d41
