<?php

namespace Helpers\Paginate;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Exception;
use Symfony\Component\HttpFoundation\Request;

class Paginate extends \Doctrine\ORM\Tools\Pagination\Paginator
{
    /**
     * @param QueryBuilder|Query $query
     * @param Request $request
     * @return Paginator
     */
    public function paginate(QueryBuilder|Query $query, Request $request): Paginator
    {
        $currentPage = $request->query->getInt('p') ?: 1;
        $limit = $request->query->getInt('per') ?: 3;
        $paginator = new Paginator($query);
        $paginator
            ->getQuery()
            ->setFirstResult($limit * ($currentPage - 1))
            ->setMaxResults($limit);

        return $paginator;
    }

    /**
     * @param Paginator $paginator
     * @return int
     */
    public function lastPage(Paginator $paginator): int
    {
        return ceil($paginator->count() / $paginator->getQuery()->getMaxResults());
    }

    /**
     * @param Paginator $paginator
     * @return int
     */
    public function total(Paginator $paginator): int
    {
        return $paginator->count();
    }

    /**
     * @param Paginator $paginator
     * @return bool
     * @throws Exception
     */
    public function currentPageHasNoResult(Paginator $paginator): bool
    {
        return !$paginator->getIterator()->count();
    }
}