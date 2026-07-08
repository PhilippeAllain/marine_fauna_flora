<?php

namespace App\Repository;

use App\Entity\Bird;
use App\Model\SearchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;


/**
 * @extends ServiceEntityRepository<Bird>
 */
class BirdRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Bird::class);
    }

    public function paginateBirds(int $page): PaginationInterface
    {

        return $this->paginator->paginate(
            $this->createQueryBuilder('b')->orderBy('b.name', 'ASC'),
            $page,
            2,
            [
                'distinct' =>  true,
                'sortFieldAllowList' => ['b.name', 'b.dci'],
            ]
        );
    }

    public function findBySearch(SearchData $searchData, int $page, int $limit): PaginationInterface
    {

        return $this->paginator->paginate(
            $this->createQueryBuilder('b')
                ->where('b.name LIKE :q')
                ->setParameter('q', "%{$searchData->q}%")
                ->orderBy('b.name', 'ASC'),
            $page,
            $limit,

        );
    }
}
