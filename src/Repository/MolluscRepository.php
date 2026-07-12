<?php

namespace App\Repository;

use App\Entity\Mollusc;
use App\Model\SearchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;


/**
 * @extends ServiceEntityRepository<Mollusc>
 */
class MolluscRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Mollusc::class);
    }

    public function paginateMolluscs(int $page): PaginationInterface
    {

        return $this->paginator->paginate(
            $this->createQueryBuilder('m')->orderBy('m.name', 'ASC'),
            $page,
            2,
            [
                'distinct' =>  true,
                'sortFieldAllowList' => ['m.name', 'm.dci'],
            ]
        );
    }

    public function findBySearch(SearchData $searchData, int $page, int $limit): PaginationInterface
    {

        return $this->paginator->paginate(
            $this->createQueryBuilder('m')
                ->where('m.name LIKE :q')
                ->setParameter('q', "%{$searchData->q}%")
                ->orderBy('m.name', 'ASC'),
            $page,
            $limit,

        );
    }
}
