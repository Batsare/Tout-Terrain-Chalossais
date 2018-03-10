<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Article::class);
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
            ->update('App:Article', 'p')
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
            ->delete('App:Article', 'p')
            ->where('p.id = ?1')
            ->setParameter('1', $id)
            ->getQuery()
            ->execute();

        return;
    }

    public function findLatest(int $page = 1): Pagerfanta
    {
        $query = $this->getEntityManager()
            ->createQuery('SELECT a FROM App:Article a ORDER BY a.id DESC')
        ;
        return $this->createPaginator($query, $page);
    }
    private function createPaginator(Query $query, int $page): Pagerfanta
    {
        $paginator = new Pagerfanta(new DoctrineORMAdapter($query));
        $paginator->setMaxPerPage(4);
        $paginator->setCurrentPage($page);
        return $paginator;
    }
}
