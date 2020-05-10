<?php

namespace App\Repository;

use App\Entity\Video;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method Video|null find($id, $lockMode = null, $lockVersion = null)
 * @method Video|null findOneBy(array $criteria, array $orderBy = null)
 * @method Video[]    findAll()
 * @method Video[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideoRepository extends ServiceEntityRepository
{
    /**
     * @var PaginatorInterface
     */
    private $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Video::class);

        $this->paginator = $paginator;
    }

    // /**
    //  * @return Video[] Returns an array of Video objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    public function findByChildIds(array $value, int $page, ?string $sortMethod): PaginationInterface
    {
        $sortMethod = $sortMethod != 'rating' ? $sortMethod : 'ASC'; //todo
        $query = $this->createQueryBuilder('v')
            ->andWhere('v.category IN (:val)')
            ->setParameter('val', $value)
            ->orderBy('v.title', $sortMethod)
            ->getQuery();

        return $this->paginator->paginate($query, $page, 5);
    }

    public function findByTitle(string $query, int $page, ?string $sort_method)
    {
        $sort_method = $sort_method != 'rating' ? $sort_method : 'ASC'; // tmp

        $queryBuilder = $this->createQueryBuilder('v');
        $searchTerms = $this->prepareQuery($query);

        foreach ($searchTerms as $key => $term)
        {
            $queryBuilder
                ->orWhere('v.title LIKE :t_'.$key)
                ->setParameter('t_'.$key, '%'.trim($term).'%');
        }

        $dbquery =  $queryBuilder
            ->orderBy('v.title', $sort_method)
            ->getQuery();

        return $this->paginator->paginate($dbquery, $page, 5);
    }

    private function prepareQuery(string $query): array
    {
        return explode(' ',$query);
    }

    public function videoDetails($id)
    {
        return $this->createQueryBuilder('v')
            ->leftJoin('v.comments', 'c')
            ->leftJoin('c.user', 'u')
            ->addSelect('c', 'c')
            ->where('v.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
