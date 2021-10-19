<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use DateTimeImmutable;
use App\Entity\Message;
use App\Repository\UserRepository;
use App\Repository\MessageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

//controller qui gère la partie administration, seulement accèsible pour les admins
/**
 * @IsGranted("ROLE_ADMIN")
 */
#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('', name: 'user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        // la vue que la méthode me retourne
        return $this->render('admin/index.html.twig', [
            // 'users' => $userRepository->findAll(),
            // ma requête personalisé qui me permet de récupérer seulement les messages non traités
            'userMessages' => $userRepository->countMessageNotDoneByUser(),
        ]);
    }

    #[Route('/done', name: 'index_done', methods: ['GET'])]
    public function viewDone(UserRepository $userRepository): Response
    {
        // la vue que la méthode me retourne
        return $this->render('admin/index_done.html.twig', [
            'controller_name' => 'UserController',
            // 'messages' => $messageRepository->findAll(),
            // ma requête personalisé qui me permet de récupérer seulement les messages traités
            'userMessages' => $userRepository->countMessageDoneByUser(),

        ]);
    }

    #[Route('/{id}', name: 'user_show', methods: ['GET'])]
    public function show(User $user, MessageRepository $messageRepository, Request $request): Response
    {
        // me permet de retourné tout les messages non traités d'un utilisateur 
        return $this->render('admin/show.html.twig', [
            'user' => $user,
            //on récupére les messages de la variable user et on les classes par date (de la + ancienne à la + récente)
            'messages' => $messageRepository->findBy(['user' => $user], ['createdAt' => 'ASC'])
        ]);
    }

    #[Route('/done/user/{id}', name: 'user_show_done', methods: ['GET'])]
    public function showDone(User $user, MessageRepository $messageRepository): Response
    {
        // me permet de retourné tout les messages traités d'un utilisateur
        return $this->render('admin/show_done.html.twig', [
            'user' => $user,
            //on récupére les messages de la variable user et on les classes par date (de la + ancienne à la + récente)
            'messages' => $messageRepository->findBy(['user' => $user], ['createdAt' => 'ASC'])
        ]);
    }

    #[Route('/user/{id}/message', name: 'message_traitement', methods: ['POST'])]
    public function traitement(Message $message): Response
    {
        //fonction qui me permet de définir les messages comme traités
        // et de définir la date a laquelle il est traité
        $em = $this->getDoctrine()->getManager();
        $message->setDone(true);
        $message->setDoneAt(new DateTimeImmutable());
        $em->flush();
        return $this->redirectToRoute('user_show', ['id' => $message->getUser()->getId()], Response::HTTP_SEE_OTHER);
    }
}
