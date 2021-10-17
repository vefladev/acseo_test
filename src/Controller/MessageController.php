<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Filesystem\Exception\IOException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/message')]
class MessageController extends AbstractController
{
    /**
     * @IsGranted("ROLE_USER")
     */
    #[Route('/new', name: 'message_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $message = new Message();
        // récupérer l'utilisateur connécté qui écrit la demande
        $user = $this->getUser();
        $message->setUser($user);
        $message->setDone(0);
        $date = new \DateTime();
        $message->setCreatedAt($date);
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($message);
            $entityManager->flush();
            $response = new Response();
            $response->setContent(json_encode([
                'data' => $message->getContent(),
            ]));
            $response = new JsonResponse([
                'Utilisateur' => $message->getUser()->getFullName(),
                'email' => $message->getUser()->getEmail(),
                "date et heure denvoi" => $message->getCreatedAt()->format('d-m-Y, H:i:s'),
                "contenu" => $message->getContent(),
            ]);
            $response->setEncodingOptions($response->getEncodingOptions() | JSON_PRETTY_PRINT);
            $fs = new Filesystem();
            try {
                $fs->dumpFile('../demandes_clients/demande_' .  $message->getCreatedAt()->format('d-m-Y, H:i:s:u') . '.json', $response);
            } catch (IOException $e) {
                $e = 'erreur' . $e->getFile();
            }
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
