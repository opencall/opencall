<?php

namespace OnCall\Bundle\AdminBundle\Repositories;

use DateTime;
use Doctrine\ORM\EntityRepository;
use OnCall\Bundle\AdminBundle\Entity\ItemAggregate;

class Counter extends EntityRepository
{
    protected function checkType($item_type)
    {
        switch ($item_type)
        {
            case 'client':
            case 'campaign':
            case 'adgroup':
            case 'advert':
                return true;
        }

        return false;
    }

    public function findAggregateSingle($item_type, $item_id, DateTime $date_from, DateTime $date_to)
    {
        // check type
        if (!$this->checkType($item_type))
            return null;

        // retrieve aggregates based on date range and item_id
        $dql = 'select sum(c.count_total) as a_total, sum(c.count_plead) as a_plead, sum(c.count_failed) as a_failed, sum(c.duration_secs) as a_duration from OnCallAdminBundle:Counter c where c.date_in >= :date_from and c.date_in <= :date_to and c.' . $item_type . '_id = :id';
        $query = $this->getEntityManager()
            ->createQuery($dql)
            ->setParameter('date_from', $date_from)
            ->setParameter('date_to', $date_to)
            ->setParameter('id', $item_id);

        $res = $query->getScalarResult();

        $ia = new ItemAggregate(
            $item_id,
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
