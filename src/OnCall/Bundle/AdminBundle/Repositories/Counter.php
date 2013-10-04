<?php

namespace OnCall\Bundle\AdminBundle\Repositories;

use DateTime;
use Doctrine\ORM\EntityRepository;
use OnCall\Bundle\AdminBundle\Entity\ItemAggregate;
use OnCall\Bundle\AdminBundle\Model\AggregateFilter;

class Counter extends EntityRepository
{
    protected function getSumDQLPrefix($item_type, $child_type)
    {
        return 'select c.' . $item_type . '_id as item_id, c.' . $child_type . '_id as child_id, sum(c.count_total) as a_total, sum(c.count_plead) as a_plead, sum(c.count_failed) as a_failed, sum(c.duration_secs) as a_duration from OnCallAdminBundle:Counter c where c.date_in >= :date_from and c.date_in <= :date_to  and c.' . $item_type . '_id = :id';
    }

    protected function createItemAggregate($id, $row)
    {
        return new ItemAggregate(
            $id,
            $row['a_total'],
            $row['a_plead'],
            $row['a_failed'],
            $row['a_duration']
        );
    }

    public function findAggregate(AggregateFilter $filter, $child_ids = array())
    {
        // retrieve aggregates based on date range and item_id
        $dql = $this->getSumDQLPrefix($filter->getItemType(), $filter->getChildrenType());

        // check if we need children
        if ($filter->needsChildren())
            $dql .= ' group by c.' . $filter->getChildrenType() . '_id';

        $query = $this->getEntityManager()
            ->createQuery($dql)
            ->setParameter('date_from', $filter->getDateFrom())
            ->setParameter('date_to', $filter->getDateTo())
            ->setParameter('id', $filter->getItemID());

        $res = $query->getScalarResult();
        error_log(print_r($res, true));

        // return single result if no children
        if (!$filter->needsChildren())
            return $this->createItemAggregate($filter->getItemID(), $res[0]);

        // for those that need the children
        $multi_ia = array();
        foreach ($res as $row)
            $multi_ia[$row['child_id']] = $this->createItemAggregate($row['child_id'], $row);

        // check child ids and create blank aggregate if needed
        foreach ($child_ids as $chid)
        {
            if (isset($multi_ia[$chid]))
                continue;

            $multi_ia[$chid] = new ItemAggregate($chid, 0, 0, 0, 0);
        }

        return $multi_ia;
    }

    public function findAggregateMultiple()
    {
    }
}
