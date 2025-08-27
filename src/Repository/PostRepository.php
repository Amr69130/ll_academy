<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }


    /** 
     * @return Post[] Returns an array of Post objects
     **/
    public function findByTypeId($typeId): array
    {
        return $this->createQueryBuilder("p")
            ->where("p.type = :typeId")
            ->setParameter("typeId", $typeId)
            ->getQuery()
            ->getResult();
    }


//    ICI LA METHODE FIND BY NAME PERMET DE TOUJOURS TROUVER MALGRE LA RECHARGE DES FIXTURES CONTRAIREMENT A FIND BY ID
    public function findByTypeName(string $typeName): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.type', 't')
            ->andWhere('t.type = :typeName')
            ->setParameter('typeName', $typeName)
            ->getQuery()
            ->getResult();
    }


    //    public function findOneBySomeField($value): ?Post
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
