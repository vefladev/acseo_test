<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * @return Message[] Returns an array of Message objects
     */
    public function sortByDate()
    {
        // requête perso pour trier les message du plus ancien au plus récent
        return $this->createQueryBuilder("m")
            ->orderBy('m.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Message[] Returns an array of Message objects
    //  */
    // public function countMessageNotDoneByUser()
    // {
    //     // requête personalisé pour récupérer seulement les messages non traités
    //     return $this->createQueryBuilder('m')
    //         ->join('m.user', 'u')
    //         ->where('m.done = 0')
    //         ->getQuery()
    //         ->getResult();
    // }
}
