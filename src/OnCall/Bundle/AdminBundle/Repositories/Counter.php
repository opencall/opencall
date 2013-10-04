<?php

namespace OnCall\Bundle\AdminBundle\Repositories;

use DateTime;
use Doctrine\ORM\EntityRepository;
use OnCall\Bundle\AdminBundle\Entity\ItemAggregate;
use OnCall\Bundle\AdminBundle\Model\AggregateFilter;

class Counter extends EntityRepository
{
    protected function getDQLPrefix($item_type, $child_type)
    {
        return 'select c.' . $item_type . '_id as item_id, c.' . $child_type . '_id as child_id';
    }

    protected function getDQLSuffix($item_type)
    {
        return ' from OnCallAdminBundle:Counter c where c.date_in >= :date_from and c.date_in <= :date_to and c.' . $item_type . '_id = :id';
    }

    protected function getDQLSumPrefix($item_type, $child_type)
    {
        return $this->getDQLPrefix($item_type, $child_type) . ', sum(c.count_total) as a_total, sum(c.count_plead) as a_plead, sum(c.count_failed) as a_failed, sum(c.duration_secs) as a_duration' . $this->getDQLSuffix($item_type);
    }

    protected function getDQLUnique($item_type, $child_type)
    {
        return $this->getDQLPrefix($item_type, $child_type) . ', count(distinct c.number_id) as a_unique' . $this->getDQLSuffix($item_type);
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
        $dql = $this->getDQLSumPrefix($filter->getItemType(), $filter->getChildrenType());
        $u_dql = $this->getDQLUnique($filter->getItemType(), $filter->getChildrenType());

        // check if we need children
        if ($filter->needsChildren())
        {
            $dql .= ' group by c.' . $filter->getChildrenType() . '_id';
            $u_dql .= ' group by c.' . $filter->getChildrenType() . '_id';
        }

        // totals and durations
        $query = $this->getEntityManager()
            ->createQuery($dql)
            ->setParameter('date_from', $filter->getDateFrom())
            ->setParameter('date_to', $filter->getDateTo())
            ->setParameter('id', $filter->getItemID());
        $res = $query->getScalarResult();

        // uniques
        $u_query = $this->getEntityManager()
            ->createQuery($u_dql)
            ->setParameter('date_from', $filter->getDateFrom())
            ->setParameter('date_to', $filter->getDateTo())
            ->setParameter('id', $filter->getItemID());
        $u_res = $u_query->getScalarResult();
        error_log(print_r($u_res, true));

        // return single result if no children
        if (!$filter->needsChildren())
        {
            $ia = $this->createItemAggregate($filter->getItemID(), $res[0]);
            $ia->setUnique($u_res[0]['a_unique']);
            return $ia;
        }

        // for those that need the children
        $multi_ia = array();
        foreach ($res as $row)
            $multi_ia[$row['child_id']] = $this->createItemAggregate($row['child_id'], $row);

        // set uniques
        foreach ($u_res as $u_row)
            $multi_ia[$u_row['child_id']]->setUnique($u_row['a_unique']);

        // check child ids and create blank aggregate if needed
        foreach ($child_ids as $chid)
        {
            if (isset($multi_ia[$chid]))
                continue;

            $multi_ia[$chid] = new ItemAggregate($chid, 0, 0, 0, 0);
        }

        return $multi_ia;
    }
}
