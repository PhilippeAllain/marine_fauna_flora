<?php

namespace App\Repository;

use App\Entity\Echinoderm;
use App\Model\SearchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;


/**
 * @extends ServiceEntityRepository<Echinoderm>
 */
class EchinodermRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Echinoderm::class);
    }

    public function paginateEchinoderms(int $page): PaginationInterface
    {

        return $this->paginator->paginate(
            $this->createQueryBuilder('e')->orderBy('e.name', 'ASC'),
            $page,
            2,
            [
                'distinct' =>  true,
                'sortFieldAllowList' => ['e.name', 'e.dci'],
            ]
        );
    }

    public function findBySearch(SearchData $searchData, int $page, int $limit): PaginationInterface
    {

        return $this->paginator->paginate(
            $this->createQueryBuilder('e')
                ->where('e.name LIKE :q')
                ->setParameter('q', "%{$searchData->q}%")
                ->orderBy('e.name', 'ASC'),
            $page,
            $limit,

        );
    }
}
