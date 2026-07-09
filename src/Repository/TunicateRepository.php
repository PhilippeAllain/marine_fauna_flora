<?php

namespace App\Repository;

use App\Entity\Tunicate;
use App\Model\SearchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;


/**
 * @extends ServiceEntityRepository<Tunicate>
 */
class TunicateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Tunicate::class);
    }

    public function paginateTunicates(int $page): PaginationInterface
    {

        return $this->paginator->paginate(
            $this->createQueryBuilder('t')->orderBy('t.name', 'ASC'),
            $page,
            2,
            [
                'distinct' =>  true,
                'sortFieldAllowList' => ['t.name', 't.dci'],
            ]
        );
    }

    public function findBySearch(SearchData $searchData, int $page, int $limit): PaginationInterface
    {

        return $this->paginator->paginate(
            $this->createQueryBuilder('t')
                ->where('t.name LIKE :q')
                ->setParameter('q', "%{$searchData->q}%")
                ->orderBy('t.name', 'ASC'),
            $page,
            $limit,

        );
    }
}
