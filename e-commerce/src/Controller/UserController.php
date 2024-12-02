<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    #[Route('/admin/user', name: 'app_user')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'users' =>$userRepository->findAll(),
        ]);
    }

    #[Route('/admin/user/{id}/to/prenium', name: 'app_user_to_prenium')]
    public function changeRole(EntityManagerInterface $entityManager, User $user): Response
    {
        $user->setRoles(["ROLE_PRENIUM", "ROLE_USER"]);
        $entityManager->flush();
        $this->addFlash('succes', "Le rôle de l'utilisateurr a été modifié");

        return $this->redirectToRoute('app_user');
    }

    #[Route('/admin/user/{id}/remove/prenium/role', name: 'app_user_remove_prenium_role')]
    public function preniumRoleRemove(EntityManagerInterface $entityManager, User $user): Response
    {
        $user->setRoles([]);
        $entityManager->flush();
        $this->addFlash('succes', "Le rôle de l'utilisateur a été retiré");

        return $this->redirectToRoute('app_user');
    }

    #[Route('/admin/user/{id}/remove', name: 'app_user_remove')]
    public function userRemove(EntityManagerInterface $entityManager,$id, UserRepository $userRepository): Response
    {
        $userFind = $userRepository->find($id);
        $entityManager->remove($userFind);
        $entityManager->flush();
        $this->addFlash('danger', "L'utilisateur a été supprimé.");

        return $this->redirectToRoute('app_user');
    }
}
