<?php

namespace App\Controller;

// use App\Entity\Message;
use App\Entity\Message;
use App\Entity\User;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin', methods: ['GET'])]
    public function index(MessageRepository $messageRepository, UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        $user->getMessages();
        dump($user);
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            // 'messages' => $messageRepository->findAll(),
            // 'users' => $userRepository->findAll(),
            'messages' => $messageRepository->findAll()
            // 'listUsers' => $listUsers
        ]);
    }

    #[Route('/admin/message_traite', name: 'message_traite', methods: ['GET'])]
    public function viewDone(MessageRepository $messageRepository): Response
    {
        return $this->render('admin/message_traite.html.twig', [
            'controller_name' => 'AdminController',
            'messages' => $messageRepository->findAll(),
        ]);
    }

    // #[Route('/admin/traitement', name: 'traitement_message', methods: ['GET'])]
    // public function traitement(Message $message): Response
    // {
    // $message = $this->get
    // return $this->render('admin/index.html.twig', [
    //     'controller_name' => 'AdminController',
    //     'messages' => $messageRepository->findAll(),
    // ]);
    // }
    #[Route('/admin/message/{id}', name: 'message_show', methods: ['GET'])]
    public function show(Message $message): Response
    {
        return $this->render('message/show.html.twig', [
            'message' => $message,
        ]);
    }

    #[Route('admin/message/{id}/edit', name: 'message_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Message $message): Response
    {
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('message/edit.html.twig', [
            'message' => $message,
            'form' => $form,
        ]);
    }

    // #[Route('admin/message/{id}', name: 'message_delete', methods: ['POST'])]
    // public function delete(Request $request, Message $message): Response
    // {
    //     if ($this->isCsrfTokenValid('delete' . $message->getId(), $request->request->get('_token'))) {
    //         $entityManager = $this->getDoctrine()->getManager();
    //         $entityManager->remove($message);
    //         $entityManager->flush();
    //     }

    //     return $this->redirectToRoute('admin', [], Response::HTTP_SEE_OTHER);
    // }
    #[Route('admin/message/{id}', name: 'message_traitement', methods: ['POST'])]
    public function traitement(Request $request, Message $message): Response
    {
        $em = $this->getDoctrine()->getManager();
        $message->setDone(true);
        $em->flush();
        dump($message);
        // $message->getId();
        // $message->setDone(1);
        // $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('admin', [], Response::HTTP_SEE_OTHER);
    }
}
