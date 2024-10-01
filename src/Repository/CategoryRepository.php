<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

      /**
       * @return CategoryWithCountDTO Returns an array of class DTO  
       */

    public function findAllWithCount(): array
     {
        return $this->createQueryBuilder('c') // je construis un querybuilder appelé c 
             ->select('NEW App\\DTO\\CategoryWithCountDTO(c.id, c.name, COUNT(c.id))') // specification des champs à recuperer et count sur l'id de la catégorie
             ->leftJoin('c.recipes', 'r') // liaison grace à leftjoin, l'orm le fait pour nous
             ->groupBy('c.id')
             ->getQuery() // pr generer ma requette
             ->getResult() // '' '' le resultat
             ;
     }

//    /**
//     * @return Category[] Returns an array of Category objects
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

//    public function findOneBySomeField($value): ?Category
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
