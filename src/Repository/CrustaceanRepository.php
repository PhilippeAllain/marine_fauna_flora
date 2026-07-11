<?php

namespace App\Repository;

use App\Entity\Crustacean;
use App\Model\SearchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;


/**
 * @extends ServiceEntityRepository<Crustacean>
 */
class CrustaceanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Crustacean::class);
    }

    public function paginateCrustaceans(int $page): PaginationInterface
    {

        return $this->paginator->paginate(
            $this->createQueryBuilder('c')->orderBy('c.name', 'ASC'),
            $page,
            2,
            [
                'distinct' =>  true,
                'sortFieldAllowList' => ['c.name', 'c.dci'],
            ]
        );
    }

    public function findBySearch(SearchData $searchData, int $page, int $limit): PaginationInterface
    {

        return $this->paginator->paginate(
            $this->createQueryBuilder('c')
                ->where('c.name LIKE :q')
                ->setParameter('q', "%{$searchData->q}%")
                ->orderBy('c.name', 'ASC'),
            $page,
            $limit,

        );
    }
}
