<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    #[Route('/api/users/{id}', name: 'getUserById', methods: [Request::METHOD_GET])]
    public function getUserById(int $id, UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        $user = $userRepository->find($id);

        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $userJson = $serializer->serialize($user, 'json', ['groups' => 'user']);

        return new JsonResponse($userJson, JsonResponse::HTTP_OK, [], true);
    }
    
    #[Route('/api/users', name: 'registration', methods: [Request::METHOD_POST])]
    public function registration(Request $request, SerializerInterface $serializer, EntityManagerInterface $em,
        UrlGeneratorInterface $urlGenerator, UserPasswordHasherInterface $hasher, ValidatorInterface $validator): Response {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email']) || !isset($data['password']) || !isset($data['firstname']) || !isset($data['lastname'])) {
            return new JsonResponse(['message' => 'All the fields are required to create an account'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $user->setEmail($data['email']);
        $user->setFirstname($data['firstname']);
        $user->setLastname($data['lastname']);
        $user->setRoles(['ROLE_USER']);

        $hashPassword = $hasher->hashPassword($user, $data['password']);
        $user->setPassword($hashPassword);

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return new JsonResponse(['message' => $errorsString], JsonResponse::HTTP_BAD_REQUEST);
        }

        $em->persist($user);
        $em->flush();

        $location = $urlGenerator->generate('getUserById', ['id' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        $jsonContent = $serializer->serialize($user, 'json');

        $responseArray = json_decode($jsonContent, true);

        return new JsonResponse($responseArray, Response::HTTP_CREATED, ['Location' => $location]);
    }
}
