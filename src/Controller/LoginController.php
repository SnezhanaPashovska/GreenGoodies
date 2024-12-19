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
        $loginForm = $this->createForm(LoginType::class, $user);

        $loginForm->handleRequest($request);

        if ($loginForm->isSubmitted() && $loginForm->isValid()) {
            return $this->redirectToRoute('app_home');

            $error = $authenticationUtils->getLastAuthenticationError();

            return $this->render('user/login.html.twig', [
                'loginForm' => $loginForm->createView(),
                'email' => $email,
                'error' => $error,
            ]);
        }
    }
}
