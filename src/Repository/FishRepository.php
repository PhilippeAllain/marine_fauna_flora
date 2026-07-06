<?php

namespace App\Repository;

use App\Entity\Fish;
use App\Model\SearchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;


/**
 * @extends ServiceEntityRepository<Fish>
 */
class FishRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Fish::class);
    }

    public function paginateFishes(int $page): PaginationInterface
    {

        return $this->paginator->paginate(
            $this->createQueryBuilder('f')->orderBy('f.name', 'ASC'),
            $page,
            2,
            [
                'distinct' =>  true,
                'sortFieldAllowList' => ['f.name', 'f.dci'],
            ]
        );
    }

    public function findBySearch(SearchData $searchData, int $page, int $limit): PaginationInterface
    {

        return $this->paginator->paginate(
            $this->createQueryBuilder('f')
                ->where('f.name LIKE :q')
                ->setParameter('q', "%{$searchData->q}%")
                ->orderBy('f.name', 'ASC'),
            $page,
            $limit,

        );
    }
}
