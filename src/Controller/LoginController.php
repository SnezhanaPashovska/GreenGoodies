<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(Request $request, EntityManagerInterface $entityManager, AuthenticationUtils $authenticationUtils): Response
    {
        
        $email = $authenticationUtils->getLastUsername();
        

        $user = new User();
        //$user->setEmail($email); // Set the email value here

        $loginForm = $this->createForm(LoginType::class, $user);

        $loginForm->handleRequest($request);

        if ($loginForm->isSubmitted() && $loginForm->isValid()) {
            // Here, Symfony will handle the authentication automatically via the firewall
            return $this->redirectToRoute('app_home'); // Change to your desired route after login
        }

        $error = $authenticationUtils->getLastAuthenticationError();

        // Return the form view along with any necessary information
        return $this->render('user/login.html.twig', [
            'loginForm' => $loginForm->createView(),
            'email' => $email,
            'error' => $error,
        ]);
    }
}
