<?php

namespace App\Repository;

use App\Entity\Payment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Payment>
 */
class PaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    public function countByPeriodAndStatus($period, string $status): int
    {
        return (int) $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->join('p.enrollment', 'e')
            ->andWhere('p.status = :status')
            ->andWhere('e.enrollmentPeriod = :period')
            ->setParameter('status', $status)
            ->setParameter('period', $period)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findByPeriodAndStatus($period, string $status)
    {
        // cela récupère les paiements filtrés par période et status
        return $this->createQueryBuilder('p')
            ->innerJoin('p.enrollment', 'e') // joindre l'inscription associée
            ->addSelect('e')
            ->innerJoin('e.student', 's') // joindre l'étudiant pour info
            ->addSelect('s')
            ->andWhere('e.enrollmentPeriod = :period')
            ->andWhere('p.status = :status')
            ->setParameter('period', $period)
            ->setParameter('status', $status)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }


    //    /**
    //     * @return Payment[] Returns an array of Payment objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Payment
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
