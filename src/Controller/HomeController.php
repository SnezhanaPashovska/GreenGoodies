<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Controller for managing the homepage.
 */

class HomeController extends AbstractController
{
    /**
     * @var ProductRepository
     */

    private $productRepository;

    /**
     * HomeController constructor.
     *
     * @param ProductRepository $productRepository The product repository to access product data.
     */

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Displays the homepage with a list of products.
     *
     * @return Response The rendered homepage template with products.
     */

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $products = $this->productRepository->findAll();
        return $this->render('home/index.html.twig', [
            'products' => $products,
        ]);
    }
}
