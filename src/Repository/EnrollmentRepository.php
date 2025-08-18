<?php

namespace App\Repository;

use App\Entity\Enrollment;
use App\Entity\EnrollmentPeriod;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Enrollment>
 */
class EnrollmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Enrollment::class);
    }

    public function countByPeriodAndStatus($period, string $status): int
    {
        return (int) $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->andWhere('e.status = :status')
            ->andWhere('e.enrollmentPeriod = :period')
            ->setParameter('status', $status)
            ->setParameter('period', $period)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findByPeriodAndStatus(EnrollmentPeriod $period, string $status)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.enrollmentPeriod = :period')
            ->andWhere('e.status = :status')
            ->setParameter('period', $period)
            ->setParameter('status', $status)
            ->orderBy('e.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Enrollment[] Returns an array of Enrollment objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Enrollment
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
