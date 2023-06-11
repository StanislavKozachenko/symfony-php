<?php

namespace Helpers;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class Sort extends \Doctrine\ORM\QueryBuilder
{
    /**
     * @throws Query\QueryException
     */
    public static function sorting(string $sortType, string $sortItemName, QueryBuilder|Query $query): QueryBuilder
    {
        $criteria = Criteria::create()
            ->orderBy([$sortItemName => $sortType === 'ASC' ? Criteria::ASC : Criteria::DESC]);
        return $query->addCriteria($criteria);
    }
}