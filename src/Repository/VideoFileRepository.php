<?php

namespace App\Repository;

use App\Entity\VideoFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method VideoFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method VideoFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method VideoFile[]    findAll()
 * @method VideoFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideoFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VideoFile::class);
    }

    // /**
    //  * @return VideoFile[] Returns an array of VideoFile objects
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

    /*
    public function findOneBySomeField($value): ?VideoFile
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
