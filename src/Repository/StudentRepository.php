<?php

namespace App\Repository;

use App\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Student>
 */
class StudentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Student::class);
    }




//    ICI LA METHODE QUI RECUPERE TOUS LES ETUDIANTS MËME NON INSCRITS (inutilisée pour le moment)
    public function findAllWithParentsAndEnrollments(): array
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.user', 'u')
            ->addSelect('u')
            ->leftJoin('s.enrollments', 'e')
            ->addSelect('e')
            ->getQuery()
            ->getResult();
    }
//    ICI LA METHODE QUI RECUPERE TOUS LES ETUDIANTS MËME NON INSCRITS (inutilisée pour le moment)




    public function findAllWithEnrollmentsOnly(): array
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.enrollments', 'e') // ce inner join va joindre que ceux qui ont une inscription
            ->addSelect('e')
            ->leftJoin('s.user', 'u')
            ->addSelect('u')
            ->getQuery()
            ->getResult();
    }

    public function findWithEnrollmentsByPeriod($period): array
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.enrollments', 'e')
            ->addSelect('e')
            ->leftJoin('s.user', 'u')
            ->addSelect('u')
            ->andWhere('e.enrollmentPeriod = :period')
            ->setParameter('period', $period)
            ->getQuery()
            ->getResult();
    }


    public function countByPeriod($period): int
    {
        return (int) $this->createQueryBuilder('s')
            ->select('COUNT(DISTINCT s.id)')
            ->join('s.enrollments', 'e')
            ->andWhere('e.enrollmentPeriod = :period')
            ->setParameter('period', $period)
            ->getQuery()
            ->getSingleScalarResult();
    }

    //    /**
    //     * @return Student[] Returns an array of Student objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Student
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
