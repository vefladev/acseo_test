<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\Message;
use App\Repository\UserRepository;
use App\Repository\MessageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_ADMIN")
 */
#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository, MessageRepository $messageRepository): Response
    {
        // la vue que la méthode me retourne
        return $this->render('user/index.html.twig', [
            // 'users' => $userRepository->findAll(),
            // ma requête personalisé qui me permet de récupérer seulement les messages non traités
            'userMessages' => $userRepository->countMessageNotDoneByUser(),
        ]);
    }

    #[Route('/done', name: 'index_done', methods: ['GET'])]
    public function viewDone(UserRepository $userRepository): Response
    {
        // la vue que la méthode me retourne
        return $this->render('user/index_done.html.twig', [
            'controller_name' => 'UserController',
            // 'messages' => $messageRepository->findAll(),
            // ma requête personalisé qui me permet de récupérer seulement les messages traités
            'userMessages' => $userRepository->countMessageDoneByUser(),

        ]);
    }

    #[Route('/new', name: 'user_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'user_show', methods: ['GET'])]
    public function show(User $user, MessageRepository $messageRepository): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
            //on récupére les messages de la variable user et on les classes par date (de la + ancienne à la + récente)
            'messages' => $messageRepository->findBy(['user' => $user], ['createdAt' => 'ASC'])
        ]);
    }

    #[Route('done/{id}', name: 'user_show_done', methods: ['GET'])]
    public function showDone(User $user, MessageRepository $messageRepository): Response
    {
        return $this->render('user/show_done.html.twig', [
            'user' => $user,
            //on récupére les messages de la variable user et on les classes par date (de la + ancienne à la + récente)
            'messages' => $messageRepository->findBy(['user' => $user], ['createdAt' => 'ASC'])
        ]);
    }

    #[Route('/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('user/{id}/message', name: 'message_traitement', methods: ['POST'])]
    public function traitement(Request $request, Message $message): Response
    {
        $em = $this->getDoctrine()->getManager();
        $message->setDone(true);
        $em->flush();

        return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
    }
}
