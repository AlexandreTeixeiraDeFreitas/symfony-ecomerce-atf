<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function save(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Product[] Returns an array of Product objects
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

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

// public function findProduct($name, $seller, $category, $brand): array
// {
//      if ($name != NULL && $seller != NULL && $category != NULL && $brand != NULL){
//          return $this->createQueryBuilder('a')
//              ->andWhere('a.name LIKE :val')
//              ->andWhere('a.seller = :val1')
//              ->andWhere('a.category = :val2')
//              ->setParameter('val', "%" . $name . "%")
//              ->setParameter('val1', $seller)
//              ->setParameter('val2', $category)
//              ->getQuery()
//              ->getResult()
//          ;
//      }elseif($name == NULL && $seller != NULL && $category != NULL){
         
//          return $this->createQueryBuilder('a')
//              ->andWhere('a.seller = :val1')
//              ->andWhere('a.category = :val2')
//              ->setParameter('val1', $seller)
//              ->setParameter('val2', $category)
//              ->getQuery()
//              ->getResult()
//          ;
//      }elseif($name == NULL && $seller == NULL && $category != NULL){
//          return $this->createQueryBuilder('a')
//              ->andWhere('a.category = :val2')
//              ->setParameter('val2', $category)
//              ->getQuery()
//              ->getResult()
//          ;
//      }elseif($name == NULL && $seller != NULL && $category == NULL){
//          return $this->createQueryBuilder('a')
//          ->andWhere('a.seller = :val1')
//          ->setParameter('val1', $seller)
//          ->getQuery()
//          ->getResult()
//      ; 
//      }elseif($name != NULL && $seller == NULL && $category == NULL){
//          return $this->createQueryBuilder('a')
//              ->andWhere('a.name LIKE :val')
//              ->setParameter('val', "%" . $name . "%")
//              ->getQuery()
//              ->getResult()
//          ;
//      }elseif($name != NULL && $seller == NULL && $category != NULL){
//          return $this->createQueryBuilder('a')
//              ->andWhere('a.name LIKE :val')
//              ->setParameter('val', "%" . $name . "%")
//              ->andWhere('a.category = :val2')
//              ->setParameter('val2', $category)
//              ->getQuery()
//              ->getResult()
//          ;
//      }elseif($name != NULL && $seller != NULL && $category == NULL){
//          return $this->createQueryBuilder('a')
//              ->andWhere('a.name LIKE :val')
//              ->setParameter('val', "%" . $name . "%")
//              ->andWhere('a.seller = :val1')
//              ->setParameter('val1', $seller)
//              ->getQuery()
//              ->getResult()
//          ;
//      }
// }
    public function findProduct($category): array
    {
        var_dump($category);
        return $this->createQueryBuilder('p')
            ->andWhere('p.category = :val')
            ->setParameter('val', $category)
            // ->orderBy('p.category', 'ASC')
            // ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
}
