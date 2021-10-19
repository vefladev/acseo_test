<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Filesystem\Exception\IOException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_USER")
 */
#[Route('/message')]
class MessageController extends AbstractController
{
    // controller qui gère l'ajout de message, seulement accessible si on est connecté (ROLE_USER)
    #[Route('/new', name: 'message_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $message = new Message();
        // récupérer l'utilisateur connécté qui écrit la demande
        $user = $this->getUser();
        // on l'ajout au message
        $message->setUser($user);
        // on défini traité sur false
        $message->setDone(0);
        // on créer une date
        $date = new \DateTime();
        // on la lie au message
        $message->setCreatedAt($date);
        // on crée le formulaire en fonction du formulaire MessageType
        $form = $this->createForm(MessageType::class, $message);
        // requête
        $form->handleRequest($request);
        // si le formulaire est valide, alors on enregister en bdd
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($message);
            $entityManager->flush();
            // on créer une variable avec les données que l'on veut récupérer (array)
            $datas = ([
                'Demande' => [
                    'Utilisateur' => $message->getUser()->getFullName(),
                    'Email' => $message->getUser()->getEmail(),
                    "Date et heure denvoi" => $message->getCreatedAt()->format('d-m-Y, H:i:s'),
                    "Contenu" => $message->getContent(),
                ]
            ]);
            //on encode les données en json et échape les charactères
            $json = json_encode($datas, JSON_UNESCAPED_UNICODE);
            $fs = new Filesystem();
            // on enregistre les données dans un dossier->fichier que l'on appelle demande_ + la date
            try {
                $fs->dumpFile('../demandes_clients/demande_' .  $message->getCreatedAt()->format('d-m-Y, H:i:s:u') . '.json', $json);
            } catch (IOException $e) {
                // sinon on retourne une erreur
                $e = 'erreur' . $e->getFile();
            }
            $this->addFlash('success', 'Votre demande a bien été envoyé');
            // puis enfin on redirige si tout c'est bien passé
            return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
            // sinon on retourne une erreur 
        } else {
            $this->addFlash('danger', "Il y a un problème votre demande n'a pas pu être envoyé");
        }
        // la vue formulaire d'un nouveau message
        return $this->renderForm('message/new.html.twig', [
            'message' => $message,
            'form' => $form,
        ]);
    }

    #[Route('/user', name: 'show_user_index', methods: ['GET'])]
    public function index(MessageRepository $messageRepository): Response
    {
        // méthode me permétant d'afficher tout les message de l'user connecté
        $user = $this->getUser();
        // la vue que la méthode me retourne
        return $this->render('message/index.html.twig', [
            // ma requête personalisé qui me permet de récupérer seulement les messages du user connecté
            'messages' => $messageRepository->findBy(['user' => $user], ['createdAt' => 'ASC'])
        ]);
    }
}
