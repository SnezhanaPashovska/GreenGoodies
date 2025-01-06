<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Controller for managing user account-related actions.
 */

class UserController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    /**
     * UserController constructor.
     * 
     * @param EntityManagerInterface $entityManager The entity manager used to interact with the database.
     */

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Displays the user's account page with their orders.
     *
     * @param PaginatorInterface $paginator The paginator service for pagination.
     * @param Request $request The HTTP request object.
     *
     * @return Response The response object containing the rendered account page.
     */

    #[Route('/account', name: 'app_account')]
    public function account(PaginatorInterface $paginator, Request $request): Response
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

    /**
     * Activates API access for the current user.
     *
     * @return Response The response object redirecting back to the account page with a success flash message.
     */

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

    /**
     * Deactivates API access for the current user.
     *
     * @return Response The response object redirecting back to the account page with a success flash message.
     */

    #[Route('/deactivate-api', name: 'deactivate_api')]
    public function deactivateApi(): Response
    {
        $user = $this->getUser();

        if ($user) {
            $user->setApiAccess(false);

            // Persist the changes
            $this->entityManager->flush();

            $this->addFlash('success', 'L\'accès à l\'API a été désactivé avec succès !');
        }

        return $this->redirectToRoute('app_account');
    }

    /**
     * Deletes the user's account and logs them out.
     *
     * @param int $id The ID of the user to delete.
     * @param Request $request The HTTP request object.
     * @param SessionInterface $session The session service to invalidate the session.
     * @param TokenStorageInterface $tokenStorage The token storage service to invalidate the authentication token.
     *
     * @return Response The response object redirecting to the home page after account deletion.
     */

    #[Route('/delete-account/{id}', name: 'delete_account', methods: ['POST'])]
    public function deleteAccount(int $id, SessionInterface $session, TokenStorageInterface $tokenStorage): Response
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
