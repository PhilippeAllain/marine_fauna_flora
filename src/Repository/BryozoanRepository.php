<?php

namespace App\Repository;

use App\Entity\Bryozoan;
use App\Model\SearchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;


/**
 * @extends ServiceEntityRepository<Bryozoan>
 */
class BryozoanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Bryozoan::class);
    }

    public function paginateBryozoans(int $page): PaginationInterface
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
