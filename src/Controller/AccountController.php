<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Form\EditAccountType;
use App\Form\ChangePasswordType;
use App\Repository\ReservationRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    #[IsGranted(attribute:"ROLE_USER")]
    public function index(): Response
    {
        return $this->render('account/account.html.twig', [
            'controller_name' => 'AccountController',
        ]);
    }


    #[Route('/account/edit', name: 'app_account_edit')]
    #[IsGranted(attribute:"ROLE_USER")]
    public function edit_account(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(EditAccountType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre compte a été mis à jour avec succès.');

            return $this->redirectToRoute('app_account');
        }

        return $this->render('account/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/account/password', name: 'app_account_password')]
    #[IsGranted(attribute:"ROLE_USER")]
    public function changePassword(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if ($passwordHasher->isPasswordValid($user, $data['oldPassword'])) {
                $hashedPassword = $passwordHasher->hashPassword($user, $data['newPassword']);
                $user->setPassword($hashedPassword);

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Votre mot de passe a été mis à jour avec succès.');

                return $this->redirectToRoute('app_account');
            } else {
                $this->addFlash('error', 'Ancien mot de passe incorrect.');
            }
        }

        return $this->render('account/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

   
}
