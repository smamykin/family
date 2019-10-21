<?php

namespace App\Repository;

use App\Entity\PostSetting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PostSetting|null find($id, $lockMode = null, $lockVersion = null)
 * @method PostSetting|null findOneBy(array $criteria, array $orderBy = null)
 * @method PostSetting[]    findAll()
 * @method PostSetting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostSettingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostSetting::class);
    }

    // /**
    //  * @return PostSetting[] Returns an array of PostSetting objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PostSetting
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
