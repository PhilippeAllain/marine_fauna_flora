<?php

namespace App\Repository;

use App\Entity\Mammal;
use App\Model\SearchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;


/**
 * @extends ServiceEntityRepository<Mammal>
 */
class MammalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Mammal::class);
    }

    public function paginateMammals(int $page): PaginationInterface
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
