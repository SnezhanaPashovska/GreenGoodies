<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $loginForm = $this->createForm(LoginType::class, $user);
       
        return $this->render('user/login.html.twig', [
            'loginForm' => $loginForm->createView(),
        ]);
    }
}
