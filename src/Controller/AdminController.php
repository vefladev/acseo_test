<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\MessageRepository;
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
    public function index(MessageRepository $messageRepository): Response
    {
        // $message = $messageRepository->findBy(['user' => '70']);
        dump($messageRepository->countMessageByUser());
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            // 'messages' => $messageRepository->sortByDate()
            'messages' => $messageRepository->groupByUser(),
            'count' => $messageRepository->countMessageByUser()
        ]);
    }

    // #[Route('/admin/message_traite', name: 'message_traite', methods: ['GET'])]
    // public function viewDone(MessageRepository $messageRepository): Response
    // {
    //     return $this->render('admin/message_traite.html.twig', [
    //         'controller_name' => 'AdminController',
    //         'messages' => $messageRepository->findAll(),
    //     ]);
    // }

    #[Route('/admin/show_messages', name: 'show_messages', methods: ['GET'])]
    public function showMessages(MessageRepository $messageRepository): Response
    {
        return $this->render('admin/show_messages.html.twig', [
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

        return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
    }
}
