<?php

namespace OnCall\Bundle\AdminBundle\Repositories;

use DateTime;
use DatePeriod;
use DateInterval;
use Doctrine\ORM\EntityRepository;
use OnCall\Bundle\AdminBundle\Model\ItemAggregate;
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
        return $this->getDQLPrefix($item_type, $child_type) . ', count(distinct c.caller_id) as a_unique' . $this->getDQLSuffix($item_type);
    }

    protected function getDQLChartPrefix($item_type, $child_type)
    {
        return $this->getDQLPrefix($item_type, $child_type) . ', sum(c.count_total) as a_total, sum(c.count_plead) as a_plead, sum(c.count_failed) as a_failed, substring(c.date_in, 1, 10) as daily, substring(c.date_in, 12, 2) as hourly' . $this->getDQLSuffix($item_type);
    }

    protected function createItemAggregate($id, $row)
    {
        if (isset($row['a_duration']))
            $duration = $row['a_duration'];
        else
            $duration = 0;

        return new ItemAggregate(
            $id,
            $row['a_total'],
            $row['a_plead'],
            $row['a_failed'],
            $duration
        );
    }

    public function findItemAggregate(AggregateFilter $filter, $child_ids = array())
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

    public function findChartAggregate(AggregateFilter $filter)
    {
        $dql = $this->getDQLChartPrefix($filter->getItemType(), $filter->getChildrenType());

        if ($filter->isDaily())
            $dql .= ' group by daily';
        else
            $dql .= ' group by hourly';

        $query = $this->getEntityManager()
            ->createQuery($dql)
            ->setParameter('date_from', $filter->getDateFrom())
            ->setParameter('date_to', $filter->getDateTo())
            ->setParameter('id', $filter->getItemID());
        $res = $query->getScalarResult();

        $multi_ia = array();
        if ($filter->isDaily())
        {
            // cycle through results
            foreach ($res as $row)
                $multi_ia[$row['daily']] = $this->createItemAggregate(0, $row);

            // make sure all days have a value
            $period = new DatePeriod(
                $filter->getDateFrom(),
                new DateInterval('P1D'), 
                $filter->getDateTo()
            );
            foreach ($period as $day)
            {
                $day_index = $day->format('Y-m-d');
                if (!isset($multi_ia[$day_index]))
                    $multi_ia[$day_index] = new ItemAggregate(0, 0, 0, 0, 0);
            }
        }
        else
        {
            // cycle through results
            foreach ($res as $row)
                $multi_ia[$row['hourly'] + 0] = $this->createItemAggregate(0, $row);

            // make sure all hours have a value
            for ($i = 0; $i < 23; $i++)
            {
                if (!isset($multi_ia[$i]))
                    $multi_ia[$i] = new ItemAggregate(0, 0, 0, 0, 0);
            }
        }

        // sort
        ksort($multi_ia);

        return $multi_ia;
    }
}
