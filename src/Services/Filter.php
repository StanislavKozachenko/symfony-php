<?php

use Doctrine\Common\Collections\Criteria;

class Filter extends \Doctrine\ORM\QueryBuilder
{
    public static function filtering($query, $filterType = "", $name = ""){
        if($name == "") {
            $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('customer', $filterType));
        } else if($filterType == ""){
            $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('name', $name));
        } else {
            $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('customer', $filterType))
                ->andWhere(Criteria::expr()->eq('name', $name));
        }
        return $query->addCriteria($criteria);
    }
}