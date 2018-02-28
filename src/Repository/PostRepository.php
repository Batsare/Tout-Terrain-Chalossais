<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class PostRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('p')
            ->where('p.something = :value')->setParameter('value', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    public function postPublished(){
        return $this->createQueryBuilder('c')
            ->where('c.published = ?1')
            ->andWhere('c.archived = ?2')
            ->orderBy('c.id','DESC')
            ->setParameter('1', true)
            ->setParameter('2', false)
            ->getQuery()
            ->execute();

    }

    public function lastPostForArchive(){
        $lastPost = $this->findBy([], ['id' => 'DESC'],1, 4);

        $this->createQueryBuilder('c')
            ->update('App:Post', 'p')
            ->set('p.archived','true')
            ->set('p.published','false')
            ->where('p.id = ?1')
            ->setParameter('1', $lastPost)
            ->getQuery()
            ->execute();
        return;

    }

    public function deleteById($id){
        $this->createQueryBuilder('c')
            ->delete('App:Post', 'p')
            ->where('p.id = ?1')
            ->setParameter('1', $id)
            ->getQuery()
            ->execute();

        return;
    }
}
