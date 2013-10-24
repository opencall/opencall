<?php

namespace OnCall\Bundle\AdminBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Plivo\Log\Filter;

class CallLog extends EntityRepository
{
    public function findLatest($client_id, Filter $filter, $limit = 100, $offset = 0)
    {
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
    }
}
