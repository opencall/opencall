<?php

namespace OnCall\Bundle\AdminBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Plivo\Log\Filter;

class CallLog extends EntityRepository
{
    protected function filterItem(Filter $filter, $qb)
    {
        // campaign
        if ($filter->getCID() != null)
        {
            $qb->andWhere('cl.campaign_id = :campaign_id')
                ->setParameter('campaign_id', $filter->getCID());
        }

        // adgroup
        if ($filter->getAGID() != null)
        {
            $qb->andWhere('cl.adgroup_id = :adgroup_id')
                ->setParameter('adgroup_id', $filter->getAGID());
        }

        // advert
        if ($filter->getAdID() != null)
        {
            $qb->andWhere('cl.advert_id = :advert_id')
                ->setParameter('advert_id', $filter->getAdID());
        }
    }

    protected function filterDuration(Filter $filter, $qb)
    {
        // duration
        $dmod = $filter->getDMod();
        if ($dmod != null && $filter->getDSec() != null)
        {
            $dmod_string = '=';
            switch ($dmod)
            {
                case 'less':
                    $dmod_string = '<';
                    break;
                case 'greater':
                    $dmod_string = '>';
                    break;
                case 'equal':
                    $dmod_string = '=';
                    break;
            }

            $qb->andWhere('cl.duration ' . $dmod_string . ' :duration')
                ->setParameter('duration', $filter->getDSec());
        }
    }

    protected function filterNumber(Filter $filter, $qb)
    {
        // number
        if ($filter->getNum() != null)
        {
            $num = preg_replace("/[^0-9,.]/", '', $filter->getNum());
            $qb->andWhere('cl.dialled_number like :number or cl.origin_number like :number or cl.destination_number like :number')
                ->setParameter('number', '%' . $num . '%');
        }
    }

    protected function filterFailed(Filter $filter, $qb)
    {
        // failed
        if ($filter->isFailed())
        {
            $qb->andWhere('cl.status in (:status)')
                ->setParameter('status', array('busy', 'failed', 'timeout', 'no-answer', 'cancel'));
        }
    }

    protected function filterHCause(Filter $filter, $qb)
    {
        // hangup cause
        if ($filter->getHCause() != null)
        {
            $qb->andWhere('cl.hangup_cause = :hangup_cause')
                ->setParameter('hangup_cause', $filter->getHCause());
        }
    }

    public function findLatest($client_id, Filter $filter, $last_id = null, $limit = 10, $offset = 0)
    {
        // create query and set client_id
        $qb = $this->createQueryBuilder('cl')
            ->where('cl.client_id = :client_id')
            ->setParameter('client_id', $client_id);

        // last id
        if ($last_id != null)
        {
            $qb->andWhere('cl.id < :last_id')
                ->setParameter('last_id', $last_id);
        }

        // filters
        $this->filterItem($filter, $qb);
        $this->filterDuration($filter, $qb);
        $this->filterNumber($filter, $qb);
        $this->filterFailed($filter, $qb);
        $this->filterHCause($filter, $qb);

        // order and limit
        $qb->orderBy('cl.id', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $query = $qb->getQuery();

        return $query->getResult();
    }
}
