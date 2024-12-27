<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ProductController extends AbstractController
{
    private $productRepository;
    private $entityManager;

    public function __construct(ProductRepository $productRepository, EntityManagerInterface $entityManager)
    {
        $this->productRepository = $productRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/product', name: 'app_product_list')]
    public function showAllProducts(): Response
    {
        $products = $this->productRepository->findAll();
        dd($products);
        return $this->render('home/index.html.twig', [
            'products' => $products,
        ]);
    }

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
