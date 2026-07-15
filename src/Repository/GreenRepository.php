<?php

namespace App\Repository;

use App\Entity\Green;
use App\Model\SearchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;


/**
 * @extends ServiceEntityRepository<Green>
 */
class GreenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Green::class);
    }

    public function paginateGreens(int $page): PaginationInterface
    {

        return $this->paginator->paginate(
            $this->createQueryBuilder('g')->orderBy('g.name', 'ASC'),
            $page,
            2,
            [
                'distinct' =>  true,
                'sortFieldAllowList' => ['g.name', 'g.dci'],
            ]
        );
    }

    public function findBySearch(SearchData $searchData, int $page, int $limit): PaginationInterface
    {

        return $this->paginator->paginate(
            $this->createQueryBuilder('g')
                ->where('g.name LIKE :q')
                ->setParameter('q', "%{$searchData->q}%")
                ->orderBy('g.name', 'ASC'),
            $page,
            $limit,

        );
    }
}
