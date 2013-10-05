<?php

namespace OnCall\Bundle\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use OnCall\Bundle\AdminBundle\Model\ItemController;
use OnCall\Bundle\AdminBundle\Model\AggregateFilter;

class AdGroupController extends ItemController
{
    public function __construct()
    {
        $this->name = 'Ad Group';
        $this->top_color = 'purple';
        $this->agg_type = array(
            'parent' => AggregateFilter::TYPE_ADGROUP,
            'table' => AggregateFilter::TYPE_ADGROUP_CHILDREN,
            'daily' => AggregateFilter::TYPE_DAILY_ADGROUP,
            'hourly' => AggregateFilter::TYPE_HOURLY_ADGROUP
        );
        $this->parent_repo = 'OnCallAdminBundle:Campaign';
        $this->child_fetch_method = 'getAdGroups';
    }
}
