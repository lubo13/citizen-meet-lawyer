<?php

namespace App\Repository;

use App\Entity\AppointmentDatetime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AppointmentDatetime|null find($id, $lockMode = null, $lockVersion = null)
 * @method AppointmentDatetime|null findOneBy(array $criteria, array $orderBy = null)
 * @method AppointmentDatetime[]    findAll()
 * @method AppointmentDatetime[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppointmentDatetimeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AppointmentDatetime::class);
    }

    // /**
    //  * @return AppointmentDatetime[] Returns an array of AppointmentDatetime objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AppointmentDatetime
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
