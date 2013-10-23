<?php

namespace OnCall\Bundle\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use OnCall\Bundle\AdminBundle\Model\ItemController;
use OnCall\Bundle\AdminBundle\Model\AggregateFilter;

class AdGroupController extends ItemController
{
    public function __construct()
    {
        $this->name = 'AdGroup';
        $this->top_color = 'purple';
        $this->template = 'OnCallAdminBundle:AdGroup:index.html.twig';
        $this->agg_type = array(
            'parent' => AggregateFilter::TYPE_CAMPAIGN,
            'table' => AggregateFilter::TYPE_CAMPAIGN_CHILDREN,
            'daily' => AggregateFilter::TYPE_DAILY_CAMPAIGN,
            'hourly' => AggregateFilter::TYPE_HOURLY_CAMPAIGN
        );

        $this->parent_repo = 'OnCallAdminBundle:Campaign';
        $this->child_repo = 'OnCallAdminBundle:AdGroup';
        $this->child_fetch_method = 'getAdGroups';

        $this->url_child = 'oncall_admin_adverts';
        $this->url_parent = 'oncall_admin_adgroups';

        $this->child_id_field = 'campaign_id';
    }
}
