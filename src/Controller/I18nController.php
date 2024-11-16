<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/i18n', name: 'i18n_')]
class I18nController extends AbstractController
{
    #[Route('/{_locale}', name: 'index', requirements: ['_locale' => 'en|fr'])]
    public function index(string $_locale, TranslatorInterface $translator): Response
    {
        $message = $translator->trans('hello');

        return $this->render('i18n/index.html.twig', [
            'message' => $message
        ]);
    }

    #[Route([
        'en' => '/domain',
        'fr' => '/domaine'
    ], name: 'domain')]
    public function domain(): Response
    {

        return $this->render('i18n/domain.html.twig', [

        ]);
    }

   
}
