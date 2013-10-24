<?php

namespace OnCall\Bundle\AdminBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Plivo\Log\Filter;

class CallLog extends EntityRepository
{
    public function findLatest($client_id, Filter $filter, $limit = 100, $offset = 0)
    {
        // create query and set client_id
        $qb = $this->createQueryBuilder('cl')
            ->where('cl.client_id = :client_id')
            ->setParameter('client_id', $client_id);

        // filters
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

        // hangup cause
        if ($filter->getHCause() != null)
        {
            $qb->andWhere('cl.hangup_cause = :hangup_cause')
                ->setParameter('hangup_cause', $filter->getHCause());
        }

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

        // TODO: number

        /*
        $filter_array = array('client_id' => $client_id);

        // process filter
        if ($filter->getCID() != null)
            $filter_array['campaign_id'] = $filter->getCID();
        if ($filter->getAGID() != null)
            $filter_array['adgroup_id'] = $filter->getAGID();
        if ($filter->getAdID() != null)
            $filter_array['advert_id'] = $filter->getAdID();
        if ($filter->getHCause() != null)
            $filter_array['hangup_cause'] = $filter->getHCause();

        return $this->findBy(
            $filter_array,
            array('id' => 'DESC'),
            $limit,
            $offset
        );
        */

        // order and limit
        $qb->orderBy('cl.id', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $query = $qb->getQuery();

        return $query->getResult();
    }
}
