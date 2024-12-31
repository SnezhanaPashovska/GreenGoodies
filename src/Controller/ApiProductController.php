<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Controller for managing product-related API endpoints.
 */

class ApiProductController extends AbstractController
{
    /**
     * Retrieves all products from the database and returns them as a JSON response.
     * 
     * This endpoint is protected by the ROLE_USER access control, meaning only 
     * authenticated users with the appropriate role can access it.
     *
     * @param ProductRepository $productRepository The repository used to fetch all products.
     * @param SerializerInterface $serialize The serializer service used to convert products to JSON.
     *
     * @return JsonResponse Returns a JSON response with the serialized product data.
     */

    #[Route('/api/products', name: 'api_product', methods: [Request::METHOD_GET])]
    #[IsGranted('ROLE_USER', message: 'Vous n\'avez pas les droits suffisants pour recuperer la liste de produits')]
    public function getAllProducts(ProductRepository $productRepository, SerializerInterface $serialize): JsonResponse
    {

        $products = $productRepository->findAll();

        $jsonContent = $serialize->serialize($products, 'json', ['groups' => "product"]);

        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    }
}
