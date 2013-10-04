<?php

namespace OnCall\Bundle\AdminBundle\Repositories;

use DateTime;
use Doctrine\ORM\EntityRepository;
use OnCall\Bundle\AdminBundle\Entity\ItemAggregate;
use OnCall\Bundle\AdminBundle\Model\AggregateFilter;

class Counter extends EntityRepository
{
    public function findAggregate(AggregateFilter $filter)
    {
        // retrieve aggregates based on date range and item_id
        $dql = 'select sum(c.count_total) as a_total, sum(c.count_plead) as a_plead, sum(c.count_failed) as a_failed, sum(c.duration_secs) as a_duration from OnCallAdminBundle:Counter c where c.date_in >= :date_from and c.date_in <= :date_to and c.' . $filter->getItemType() . '_id = :id';
        $query = $this->getEntityManager()
            ->createQuery($dql)
            ->setParameter('date_from', $filter->getDateFrom())
            ->setParameter('date_to', $filter->getDateTo())
            ->setParameter('id', $filter->getItemID());

        $res = $query->getScalarResult();

        $ia = new ItemAggregate(
            $filter->getItemID(),
            $res[0]['a_total'],
            $res[0]['a_plead'],
            $res[0]['a_failed'],
            $res[0]['a_duration']
        );

        return $ia;
    }

    public function findAggregateMultiple()
    {
    }
}
