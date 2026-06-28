<?php

namespace App\Repository;

use App\Entity\Glossary;
use App\Model\SearchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;


/**
 * @extends ServiceEntityRepository<Glossary>
 */
class GlossaryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Glossary::class);
    }

    public function paginateGlossaries(int $page): PaginationInterface
    {

        return $this->paginator->paginate(
            $this->createQueryBuilder('g')->orderBy('g.word', 'ASC'),
            $page,
            2,
            [
                'distinct' =>  true,
                'sortFieldAllowList' => ['g.word'],
            ]
        );
    }

       public function findBySearch(SearchData $searchData, int $page, int $limit): PaginationInterface
    {

    return $this->paginator->paginate(
        $this->createQueryBuilder('g')
            ->where('g.word LIKE :q')
            ->setParameter('q', "%{$searchData->q}%")
            ->orderBy('g.word', 'ASC'),
        $page,
        $limit,

    );
    }

}
