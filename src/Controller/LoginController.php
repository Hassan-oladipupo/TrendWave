<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(
        AuthenticationUtils $utils
    ): Response {
        //getting the last username that was input

        $lastUsername = $utils->getLastUsername();

        //getting the last error 
        $error = $utils->getLastAuthenticationError();


        return $this->render('login/index.html.twig', [
            'lastUsername' => $lastUsername,
            'error' => $error
        ]);
    }
    #[Route('/logout', name: 'app_logout')]
    public function logout()
    {
    }
}
