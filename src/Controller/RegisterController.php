<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        // Create new user object
        $user = new User();

        // Create form
        $registrationForm = $this->createForm(RegistrationType::class, $user);
        $registrationForm->handleRequest($request);

        // Handle form submission
        if ($registrationForm->isSubmitted() && $registrationForm->isValid()) {

            // Hash the password
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);

            // Persist and flush the user entity
            $entityManager->persist($user);
            $entityManager->flush();

            // Redirect to the homepage with a success message
            $this->addFlash('success', 'Registration successful! You can now log in.');
            return $this->redirectToRoute('app_home');
        }

        // Render the registration form if not submitted or invalid
        return $this->render('user/register.html.twig', [
            'registrationForm' => $registrationForm->createView(),
        ]);
    }
}
