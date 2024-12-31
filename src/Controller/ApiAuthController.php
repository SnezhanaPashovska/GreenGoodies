<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Controller for handling user authentication via API.
 */

class ApiAuthController extends AbstractController
{
    /**
     * Login function to authenticate the user via email and password.
     *
     * This method processes the login request, checks for valid credentials, verifies if the user
     * has API access enabled, and generates a JWT token if everything is correct.
     *
     * @param Request $request The HTTP request object containing user credentials.
     * @param UserRepository $userRepository The repository to fetch user data.
     * @param UserPasswordHasherInterface $passwordHasher The service to verify the hashed password.
     * @param JWTTokenManagerInterface $jwtManager The service used to create JWT tokens.
     *
     * @return JsonResponse The response containing either an error message or the generated JWT token.
     * 
     */

    #[Route('/api/login', name: 'api_login', methods: [Request::METHOD_POST])]
    public function login(
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface $jwtManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !$password) {
            return new JsonResponse(['message' => 'Username and password are required'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = $userRepository->findOneBy(['email' => $email]);

        if (!$user || !$passwordHasher->isPasswordValid($user, $password)) {
            return new JsonResponse(['message' => 'Invalid credentials'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        if (!$user->isApiAccess()) {
            return new JsonResponse(['message' => 'API access not enabled'], JsonResponse::HTTP_FORBIDDEN);
        }

        $token = $jwtManager->create($user);

        if (!$token) {
            return new JsonResponse(['message' => 'Failed to generate token'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['token' => $token], JsonResponse::HTTP_OK);
    }
}
