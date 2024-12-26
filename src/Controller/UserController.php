<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    #[Route('/account', name: 'app_account')]
    public function account(PaginatorInterface $paginator, Request $request, ): Response
    {
        $user = $this->getUser();
        $orders = $this->entityManager->getRepository(Order::class)->findBy(
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
            'user' => $user,
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
    public function deactivateApi(): Response
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

    #[Route('/delete-account/{id}', name: 'delete_account', methods: ['POST'])]
    public function deleteAccount(int $id, Request $request, SessionInterface $session, TokenStorageInterface $tokenStorage): Response
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if ($user) {

            $session->invalidate();
            $tokenStorage->setToken(null);

            $this->entityManager->remove($user);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_home');
    }
}
