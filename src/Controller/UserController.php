<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function account(OrderRepository $orderRepository, EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request, ): Response
    {
        $user = $this->getUser();
        $orders = $entityManager->getRepository(Order::class)->findBy(
            ['user' => $user],
            ['orderDate' => 'DESC']
        );

        $pagination = $paginator->paginate(
            $orders,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('user/account.html.twig', [
            'orders' => $orders,
            'pagination' => $pagination,
        ]);
    }

}
