<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Controller for managing the cart.
 */

class CartController extends AbstractController
{

    /**
     * Adds a product to the cart or increases its quantity if already in the cart.
     *
     * @param int $id The product ID.
     * @param Request $request The HTTP request.
     * @param Product $product The product entity.
     * @param ProductRepository $productRepository The product repository.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse Redirects to the product detail page.
     */

    #[Route('/cart/add/{id}', name: 'add_to_cart', methods: ['POST'])]
    public function addToCart(int $id, Request $request, Product $product, ProductRepository $productRepository)
    {

        $session = $request->getSession();

        $cart = $session->get('cart', []);

        $product = $productRepository->find($id);

        if ($product) {
            if (isset($cart[$product->getId()])) {
                $cart[$product->getId()]['quantity']++;
            } else {
                $cart[$product->getId()] = [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'price' => $product->getPrice(),
                    'image' => $product->getImage(),
                    'quantity' => 1,
                ];
            }

            $session->set('cart', $cart);
        } else {
            $this->addFlash('error', 'Produit introuvable');
        }

        $this->addFlash('product', 'Produit ajouté au panier avec succès');

        return $this->redirectToRoute('app_product_detail', ['id' => $id]);
    }

    /**
     * Displays the products in the cart and calculates the total price.
     *
     * @param Request $request The HTTP request.
     * @param EntityManagerInterface $entityManager The entity manager to fetch product data.
     *
     * @return \Symfony\Component\HttpFoundation\Response The rendered cart template.
     */

    #[Route('/cart', name: 'view_cart')]
    public function viewCart(Request $request, EntityManagerInterface $entityManager)
    {
        $session = $request->getSession();

        $cart = $session->get('cart', []);

        $products = [];

        foreach ($cart as $item) {
            $product = $entityManager->getRepository(Product::class)->find($item['id']);

            if ($product) {
                $products[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                ];
            }
        }

        $total = 0;
        foreach ($products as $item) {
            $total += $item['product']->getPrice() * $item['quantity'];
        }

        if (empty($products)) {
            $this->addFlash('info', 'Votre panier est vide.');
        }

        return $this->render('cart/cart.html.twig', [
            'products' => $products,
            'total' => $total,
            'cart' => $cart,
        ]);
    }

    /**
     * Clears the cart from the session.
     *
     * @param Request $request The HTTP request.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse Redirects to the cart page.
     */

    #[Route('/cart/clear', name: 'clear_cart', methods: ['POST'])]
    public function clearCart(Request $request)
    {
        $session = $request->getSession();
        $session->remove('cart');

        $this->addFlash('info', 'Le panier a été vidé.');
        return $this->redirectToRoute('view_cart');
    }
}
