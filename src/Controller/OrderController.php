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

/**
 * Controller for managing orders.
 */
class OrderController extends AbstractController
{
    /**
     * Creates a new order based on the current cart in the session and stores it in the database.
     *
     * @param EntityManagerInterface $entityManager The Doctrine entity manager used for persisting data.
     * @param SessionInterface $session The session to retrieve the cart and clear it afterward.
     * @param ProductRepository $productRepository The repository to fetch product data from the database.
     *
     * @return Response Redirects to the order success page after the order is created.
     */

    #[Route('/order/create', name: 'order_create')]
    public function createOrder(EntityManagerInterface $entityManager, SessionInterface $session, ProductRepository $productRepository): Response
    {
        $cart = $session->get('cart', []);
        $totalAmount = 0;

        $productIds = array_keys($cart);

        $products = $productRepository->findBy(['id' => $productIds]);
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
        $order->setTotalAmount((string) $totalAmount);

        $entityManager->persist($order);
        $entityManager->flush();

        $session->remove('cart');

        return $this->redirectToRoute('order_success');
    }

    /**
     * Displays the order success page.
     *
     * @return Response The rendered order success page.
     */

    #[Route('/order/success', name: 'order_success')]
    public function orderSuccess(): Response
    {
        return $this->render('order/order-successful.html.twig');
    }
}
