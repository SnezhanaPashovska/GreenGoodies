<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function account(OrderRepository $orderRepository): Response
    {
        $user = $this->getUser();
        $orders = $orderRepository->findBy(['user' => $user], ['id' => 'DESC']);
        return $this->render('user/account.html.twig', [
            'orders' => $orders,
        ]);
    }

}
