<?php

namespace Helpers;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class Filter extends \Doctrine\ORM\QueryBuilder
{
    /**
     * @throws Query\QueryException
     */
    public static function filtering(QueryBuilder|Query $query, string $customer = "", string $name = ""): QueryBuilder
    {
        $criteria = Criteria::create();
        if ("" !== $name) {
            $criteria->andWhere(Criteria::expr()->eq('name', $name));
        }
        if ("" !== $customer) {
            $criteria->andWhere(Criteria::expr()->eq('customer', $customer));
        }
        return $query->addCriteria($criteria);
    }
}