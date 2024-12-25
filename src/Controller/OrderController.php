<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{
    #[Route('/order/create', name: 'order_create')]
    public function createOrder(EntityManagerInterface $entityManager, SessionInterface $session, ProductRepository $productRepository): Response
    {
        // Retrieve the cart from the session
        $cart = $session->get('cart', []);
        $totalAmount = 0;

        // Extract product IDs from the cart
        $productIds = array_keys($cart);

        // Retrieve products from the database based on product IDs
        $products = $productRepository->findBy(['id' => $productIds]);
        //Create an order
        $order = new Order();
        $order->setUser($this->getUser());
        $order->setOrderDate(new \DateTime());

        foreach ($cart as $productId => $cartItem) {
            $quantity = (int) $cartItem['quantity'];

            $product = $productRepository->find($productId);
            if ($product) {
                $price = $product->getPrice();
                $totalAmount += $price * $quantity;

                $existingOrderProduct = null;
                foreach ($order->getOrderProducts() as $orderProduct) {
                    if ($orderProduct->getProduct() === $product) {
                        $existingOrderProduct = $orderProduct;
                        break;
                    }
                }

                if ($existingOrderProduct) {
                    $existingOrderProduct->setQuantity($existingOrderProduct->getQuantity() + $quantity);
                } else {
                    $orderProduct = new OrderProduct();
                    $orderProduct->setProduct($product);
                    $orderProduct->setQuantity($quantity);
                    $orderProduct->setOrder($order);

                    $entityManager->persist($orderProduct);
                }
            }
        }
        // Set the total amount after all products are processed
        $order->setTotalAmount((string) $totalAmount);

        $entityManager->persist($order);
        $entityManager->flush();

        $session->remove('cart');

        return $this->redirectToRoute('order_success');
    }

    #[Route('/order/success', name: 'order_success')]
    public function orderSuccess(): Response
    {
        return $this->render('order/order-successful.html.twig');
    }
}
