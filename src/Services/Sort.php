<?php

use Doctrine\Common\Collections\Criteria;

class Sort extends \Doctrine\ORM\QueryBuilder
{
    public static function sorting($sortType, $sortItemName, $query){
        $criteria = Criteria::create()
            ->orderBy([$sortItemName => $sortType == 'ASC' ? Criteria::ASC : Criteria::DESC]);
        dd($criteria);
        return $query->addCriteria($criteria);
    }
}