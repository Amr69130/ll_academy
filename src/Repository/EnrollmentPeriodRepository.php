<?php

namespace App\Repository;

use App\Entity\EnrollmentPeriod;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EnrollmentPeriodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EnrollmentPeriod::class);
    }

    /**
     * Période par défaut : la dernière période ouverte (startDate la plus récente).
     */
    public function findDefaultOpenPeriod(): ?EnrollmentPeriod
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.isOpen = :open')->setParameter('open', true)
            ->orderBy('p.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
