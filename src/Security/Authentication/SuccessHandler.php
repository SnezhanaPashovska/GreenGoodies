<?php

namespace App\Security\Authentication;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class SuccessHandler implements AuthenticationSuccessHandlerInterface
{
  private JWTTokenManagerInterface $jwtManager;

  public function __construct(JWTTokenManagerInterface $jwtManager)
  {
    $this->jwtManager = $jwtManager;
  }

  public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
  {
    /** @var UserInterface $user */
    $user = $token->getUser();

    // Check if API access is enabled for the user
    if ($user->isApiAccess()) {
      // Generate and return the JWT token
      $jwt = $this->jwtManager->create($user);
      return new Response(json_encode(['token' => $jwt]), Response::HTTP_OK, [
        'Content-Type' => 'application/json'
      ]);
    }

    // If API access is not enabled, return an error message
    return new Response(json_encode(['error' => 'API access not enabled.']), Response::HTTP_FORBIDDEN, [
      'Content-Type' => 'application/json'
    ]);
  }
}
