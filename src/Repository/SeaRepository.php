<?php

namespace App\Repository;

use App\Entity\Sea;
use App\Model\SearchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Sea>
 */
class SeaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sea::class);
    }

        public function findByName(): QueryBuilder
    {
        $qb = $this->createQueryBuilder(alias:'s')
            ->orderBy('s.name', 'ASC');
   
        return $qb;
        ;
    }

        public function findBySearch(SearchData $searchData): QueryBuilder
    {
        $qb = $this->createQueryBuilder(alias: 's');


        if (!empty($searchData->q)) {
            $qb = $qb
                ->andWhere('s.name LIKE :q')
                ->setParameter('q', "%{$searchData->q}%")
                ->orderBy('s.name', 'ASC')
                ->setMaxResults(5);
        }

        return $qb;
    }

    //    /**
    //     * @return Sea[] Returns an array of Sea objects
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

    //    public function findOneBySomeField($value): ?Sea
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
