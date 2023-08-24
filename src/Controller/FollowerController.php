<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FollowerController extends AbstractController
{
    #[Route('/Follow/{id}', name: 'app_Follow')]
    public function follow(User $userToFollow, ManagerRegistry $doctrine, Request $request): Response
    {
        /** @var User $currentUser */
        //getting the current user 
        $currentUser = $this->getUser();

        ///check if the current user is not not himself , follow if he is not
        if ($userToFollow->getId() !== $currentUser->getId()) {
            $currentUser->follow($userToFollow);
            $doctrine->getManager()->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/unFollow/{id}', name: 'app_unFollow')]
    public function unFollow(User $userToUnFollow, ManagerRegistry $doctrine, Request $request): Response
    {
        /** @var User $currentUser */
        //getting the current user 
        $currentUser = $this->getUser();

        ///check if the current user is not not himself , unFollow if he is not
        if ($userToUnFollow->getId() !== $currentUser->getId()) {
            $currentUser->unFollow($userToUnFollow);
            $doctrine->getManager()->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }
}
