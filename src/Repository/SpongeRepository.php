<?php

namespace App\Repository;

use App\Entity\Sponge;
use App\Model\SearchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;


/**
 * @extends ServiceEntityRepository<Sponge>
 */
class SpongeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Sponge::class);
    }

    public function paginateSponges(int $page): PaginationInterface
    {

        return $this->paginator->paginate(
            $this->createQueryBuilder('s')->orderBy('s.name', 'ASC'),
            $page,
            2,
            [
                'distinct' =>  true,
                'sortFieldAllowList' => ['s.name', 's.dci'],
            ]
        );
    }

    public function findBySearch(SearchData $searchData, int $page, int $limit): PaginationInterface
    {

        return $this->paginator->paginate(
            $this->createQueryBuilder('s')
                ->where('s.name LIKE :q')
                ->setParameter('q', "%{$searchData->q}%")
                ->orderBy('s.name', 'ASC'),
            $page,
            $limit,

        );
    }
}
