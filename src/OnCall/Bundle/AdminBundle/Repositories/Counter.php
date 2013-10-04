<?php

namespace OnCall\Bundle\AdminBundle\Repositories;

use DateTime;
use Doctrine\ORM\EntityRepository;
use OnCall\Bundle\AdminBundle\Entity\ItemAggregate;
use OnCall\Bundle\AdminBundle\Model\AggregateFilter;

class Counter extends EntityRepository
{
    protected function getSumDQLPrefix($item_type)
    {
        return 'select c.' . $item_type . '_id as item_id, sum(c.count_total) as a_total, sum(c.count_plead) as a_plead, sum(c.count_failed) as a_failed, sum(c.duration_secs) as a_duration from OnCallAdminBundle:Counter c where c.date_in >= :date_from and c.date_in <= :date_to  and c.' . $item_type . '_id = :id';
    }

    public function findAggregate(AggregateFilter $filter, $child_ids = array())
    {
        // retrieve aggregates based on date range and item_id
        $dql = $this->getSumDQLPrefix($filter->getItemType());

        // check if we need children
        if ($filter->needsChildren())
            $dql .= ' group by c.' . $filter->getChildrenType() . '_id';

        $query = $this->getEntityManager()
            ->createQuery($dql)
            ->setParameter('date_from', $filter->getDateFrom())
            ->setParameter('date_to', $filter->getDateTo())
            ->setParameter('id', $filter->getItemID());

        $res = $query->getScalarResult();

        // return single result if no children
        if (!$filter->needsChildren())
        {
            $ia = new ItemAggregate(
                $filter->getItemID(),
                $res[0]['a_total'],
                $res[0]['a_plead'],
                $res[0]['a_failed'],
                $res[0]['a_duration']
            );

            return $ia;
        }

        // for those that need the children
        $multi_ia = array();
        foreach ($res as $row)
        {
            $ia = new ItemAggregate(
                $row['item_id'],
                $row['a_total'],
                $row['a_plead'],
                $row['a_failed'],
                $row['a_duration']
            );
            $multi_ia[$row['item_id']] = $ia;
        }

        // check child ids and create blank aggregate if needed
        foreach ($child_ids as $chid)
        {
            if (isset($multi_ia[$chid]))
                continue;

            $ia = new ItemAggregate($chid, 0, 0, 0, 0);
            $multi_ia[$chid] = $ia;
        }

        return $multi_ia;
    }

    public function findAggregateMultiple()
    {
    }
}
