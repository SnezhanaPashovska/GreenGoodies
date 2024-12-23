<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{

    #[Route('/cart/add/{id}', name: 'add_to_cart', methods: ['POST'])]
    public function addToCart(int $id, Request $request, Product $product)
    {
        // Get the session
        $session = $request->getSession();
        // Get the current cart
        $cart = $session->get('cart', []);

        // Add the product in the cart or increase if it is already in the cart
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

        // Save the updated cart to session

        $session->set('cart', $cart);

        //Message
        $this->addFlash('success', 'Produit ajouté au panier avec succès');

        // Return back same page
        return $this->redirectToRoute('app_product_detail', ['id' => $id]);

    }

    #[Route('/cart', name: 'view_cart')]
    public function viewCart(Request $request, EntityManagerInterface $entityManager)
    {
        // Get the session
        $session = $request->getSession();

        // Get the cart from the session
        $cart = $session->get('cart', []);

        // Fetch the product details from the db
        $products = [];

        foreach ($cart as $item) {
            $product = $entityManager->getRepository(Product::class)->find($item['id']);

            // If the product exists, add it to the products array with the quantity
            if ($product) {
                $products[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                ];
            }
        }

        // Total price

        $total = 0;
        foreach ($products as $item) {
            $total += $item['product']->getPrice() * $item['quantity'];
        }

        // If cart is empty
        if (empty($products)) {
            $this->addFlash('info', 'Votre panier est vide.');
        }

        // Render a template to display the cart
        return $this->render('cart/cart.html.twig', [
            'products' => $products,
            'total' => $total,
        ]);
    }

    #[Route('/cart/clear', name: 'clear_cart', methods: ['POST'])]
    public function clearCart(Request $request)
    {
        $session = $request->getSession();
        $session->remove('cart');

        $this->addFlash('success', 'Le panier a été vidé.');
        return $this->redirectToRoute('view_cart');
    }
}
