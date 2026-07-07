<?php

namespace App\Repository;

use App\Entity\Reptile;
use App\Model\SearchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;


/**
 * @extends ServiceEntityRepository<Reptile>
 */
class ReptileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Reptile::class);
    }

    public function paginateReptiles(int $page): PaginationInterface
    {

        return $this->paginator->paginate(
            $this->createQueryBuilder('r')->orderBy('r.name', 'ASC'),
            $page,
            2,
            [
                'distinct' =>  true,
                'sortFieldAllowList' => ['r.name', 'r.dci'],
            ]
        );
    }

    public function findBySearch(SearchData $searchData, int $page, int $limit): PaginationInterface
    {

        return $this->paginator->paginate(
            $this->createQueryBuilder('r')
                ->where('r.name LIKE :q')
                ->setParameter('q', "%{$searchData->q}%")
                ->orderBy('r.name', 'ASC'),
            $page,
            $limit,

        );
    }
}
