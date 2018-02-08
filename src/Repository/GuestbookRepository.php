<?php

namespace App\Repository;

use App\Entity\Guestbook;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class GuestbookRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Guestbook::class);
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('g')
            ->where('g.something = :value')->setParameter('value', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
