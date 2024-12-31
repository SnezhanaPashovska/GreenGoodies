<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

class ApiProductController extends AbstractController
{

    #[Route('/api/products', name: 'api_product', methods: [Request::METHOD_GET])]
    #[IsGranted('ROLE_USER', message: 'Vous n\'avez pas les droits suffisants pour recuperer la liste de produits')]
    public function getAllProducts(ProductRepository $productRepository, SerializerInterface $serialize): JsonResponse
    {

        $products = $productRepository->findAll();

        $jsonContent = $serialize->serialize($products, 'json', ['groups' => "product"]);

        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    }
}
