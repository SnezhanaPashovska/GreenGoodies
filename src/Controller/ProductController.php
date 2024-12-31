<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Controller for managing products.
 */

class ProductController extends AbstractController
{
    /**
     * ProductController constructor.
     *
     * @param ProductRepository $productRepository The repository to fetch products from the database.
     * @param EntityManagerInterface $entityManager The entity manager to interact with the database.
     */

    private $productRepository;

    public function __construct(ProductRepository $productRepository, EntityManagerInterface $entityManager)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Displays a list of all products.
     *
     * @return Response The rendered product list page.
     */

    #[Route('/product', name: 'app_product_list')]
    public function showAllProducts(): Response
    {
        $products = $this->productRepository->findAll();
        dd($products);
        return $this->render('home/index.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * Displays the details of a single product.
     *
     * @param int $id The ID of the product to display.
     * @param ProductRepository $productRepository The repository to fetch product details from the database.
     * @param Request $request The HTTP request object.
     * @param EntityManagerInterface $entityManager The entity manager to interact with the database.
     * @param SessionInterface $session The session to access cart data.
     *
     * @return Response The rendered product detail page.
     */

    #[Route('/product/{id}', name: 'app_product_detail')]
    public function showProductDetail(int $id, ProductRepository $productRepository, Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {

        $product = $this->productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        $cart = $session->get('cart', []);

        $quantityInCart = isset($cart[$product->getId()]) ? $cart[$product->getId()]['quantity'] : 0;

        return $this->render('product/product.html.twig', [
            'product' => $product,
            'quantityInCart' => $quantityInCart,
        ]);
    }
}
