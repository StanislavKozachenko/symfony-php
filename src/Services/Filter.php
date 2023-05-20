<?php

use Doctrine\Common\Collections\Criteria;

class Filter extends \Doctrine\ORM\QueryBuilder
{
    public static function filtering($filterType, $query){
        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->eq('customer', $filterType));
        return $query->addCriteria($criteria);

    }
}