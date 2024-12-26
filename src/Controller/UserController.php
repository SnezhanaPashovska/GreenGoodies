<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class UserController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/account', name: 'app_account')]
    public function account(EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request, ): Response
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

    #[Route('/activate-api', name: 'activate_api')]
    public function activateApi(): Response
    {
        $user = $this->getUser();

        if ($user) {
            $user->setApiAccess(true);

            $this->entityManager->flush();

            $this->addFlash('success', 'L\'accès à l\'API a été activé avec succès !');
        }

        return $this->redirectToRoute('app_account');
    }

    #[Route('/deactivate-api', name: 'deactivate_api')]
    public function deactivateApi(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if ($user) {
            $user->setApiAccess(false);

            // Persist the changes
            $entityManager->flush();

            $this->addFlash('success', 'L\'accès à l\'API a été désactivé avec succès !');
        }

        return $this->redirectToRoute('app_account');
    }

}
