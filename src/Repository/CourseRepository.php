<?php

namespace App\Repository;

use App\Entity\Course;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Course>
 */
class CourseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Course::class);
    }

    public function findAllWithEnrollmentsAndStudents()
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.enrollments', 'e')
            ->leftJoin('e.student', 's')
            ->addSelect('e', 's')
            ->getQuery()
            ->getResult();
    }

    public function countByPeriod($period, bool $onlyOpen = false): int
    {
        $qb = $this->createQueryBuilder('c')
            ->select('COUNT(DISTINCT c.id)')
            ->join('c.enrollments', 'e')
            ->andWhere('e.enrollmentPeriod = :period')
            ->setParameter('period', $period);

        if ($onlyOpen) {
            $qb->andWhere('c.isOpen = :open')->setParameter('open', true);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
    //    /**
    //     * @return Course[] Returns an array of Course objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Course
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
