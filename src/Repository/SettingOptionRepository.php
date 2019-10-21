<?php

namespace App\Repository;

use App\Entity\SettingOption;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SettingOption|null find($id, $lockMode = null, $lockVersion = null)
 * @method SettingOption|null findOneBy(array $criteria, array $orderBy = null)
 * @method SettingOption[]    findAll()
 * @method SettingOption[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SettingOptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SettingOption::class);
    }

    // /**
    //  * @return SettingOption[] Returns an array of SettingOption objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SettingOption
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
