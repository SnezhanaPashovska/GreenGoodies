<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Controller responsible for handling user login.
 */
class LoginController extends AbstractController
{
    /**
     * Displays the login form and handles login attempts.
     *
     * @param AuthenticationUtils $authenticationUtils Provides methods to get the last username and authentication error.
     *
     * @return Response The rendered login form template with email and error information.
     */

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {

        $email = $authenticationUtils->getLastUsername();

        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('user/login.html.twig', [
            'email' => $email,
            'error' => $error,
        ]);
    }

    /**
     * Handles the logout process.
     *
     * @return void
     */

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Logout
    }
}
