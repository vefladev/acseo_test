<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/message')]
class MessageController extends AbstractController
{
    /**
     * @IsGranted("ROLE_USER")
     */
    #[Route('/new', name: 'message_new', methods: ['GET','POST'])]
    public function new(Request $request): Response
    {
        $message = new Message();
        // récupérer l'utilisateur connécté qui écrit la demande
        $user = $this->getUser();
        $message->setUser($user);
        $message->setDone(0);
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($message);
            $entityManager->flush();

            $this->addFlash('success', 'Votre demande a bien été envoyé');

            return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        } else {
            $this->addFlash('danger', "Il y a un problème votre demande n'a pas pu être envoyé");
        }

        return $this->renderForm('message/new.html.twig', [
            'message' => $message,
            'form' => $form,
        ]);
    }
}
