<?php

namespace App\Repository;

use App\Entity\Plant;
use App\Model\SearchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;


/**
 * @extends ServiceEntityRepository<Plant>
 */
class PlantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Plant::class);
    }

    public function paginatePlants(int $page): PaginationInterface
    {

        return $this->paginator->paginate(
            $this->createQueryBuilder('p')->orderBy('p.name', 'ASC'),
            $page,
            2,
            [
                'distinct' =>  true,
                'sortFieldAllowList' => ['p.name', 'p.dci'],
            ]
        );
    }

    public function findBySearch(SearchData $searchData, int $page, int $limit): PaginationInterface
    {

        return $this->paginator->paginate(
            $this->createQueryBuilder('p')
                ->where('p.name LIKE :q')
                ->setParameter('q', "%{$searchData->q}%")
                ->orderBy('p.name', 'ASC'),
            $page,
            $limit,

        );
    }
}
