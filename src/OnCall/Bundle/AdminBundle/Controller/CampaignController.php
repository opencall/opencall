<?php

namespace OnCall\Bundle\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use OnCall\Bundle\AdminBundle\Model\ItemController;
use OnCall\Bundle\AdminBundle\Model\MenuHandler;
use OnCall\Bundle\AdminBundle\Entity\Client;
use OnCall\Bundle\AdminBundle\Entity\Campaign;
use OnCall\Bundle\AdminBundle\Model\ItemStatus;
use OnCall\Bundle\AdminBundle\Model\AggregateFilter;

class CampaignController extends ItemController
{
    public function __construct()
    {
        $this->name = 'Campaign';
        $this->top_color = 'blue';
        $this->agg_type = array(
            'parent' => AggregateFilter::TYPE_CLIENT,
            'table' => AggregateFilter::TYPE_CLIENT_CHILDREN,
            'daily' => AggregateFilter::TYPE_DAILY_CLIENT,
            'hourly' => AggregateFilter::TYPE_HOURLY_CLIENT
        );

        $this->parent_repo = 'OnCallAdminBundle:Client';
        $this->child_repo = 'OnCallAdminBundle:Campaign';
        $this->child_fetch_method = 'getCampaigns';

        $this->url_child = 'oncall_admin_adgroups';
        $this->url_parent = 'oncall_admin_campaigns';
    }
}
