<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function account(OrderRepository $orderRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser(); 
        $orders = $entityManager->getRepository(Order::class)->findBy(
            ['user' => $user], 
            ['orderDate' => 'DESC']
        );

        return $this->render('user/account.html.twig', [
            'orders' => $orders, 
        ]);
    }

}
