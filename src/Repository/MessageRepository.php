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
    public function groupByUser()
    {
        return $this->createQueryBuilder('m')
            ->select('u')
            ->from(Message::class, 'u', 'u.id')
            ->groupBy('u')
            ->getQuery()
            ->getResult();
        // createQueryBuilder('m')
        //     ->select("user")
        // ->join("message.user", "user")
        // ->where('message.user = :user')
        // ->groupBy("user")
        // ->setParameters($parameters)
        // ->getQuery();
        // ->getResult();

        // return $this->createQueryBuilder('m')
        //     ->andWhere('m.user = :user')
        //     // ->setParameter('message',)
        //     ->groupBy('m.email')
        //     ->setMaxResults(10)
        //     ->getQuery()
        //     ->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Message
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
