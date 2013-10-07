<?php

namespace OnCall\Bundle\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use OnCall\Bundle\AdminBundle\Model\ItemController;
use OnCall\Bundle\AdminBundle\Model\AggregateFilter;

class AdvertController extends ItemController
{
    public function __construct()
    {
        $this->name = 'Advert';
        $this->top_color = 'yellow';
        $this->template = 'OnCallAdminBundle:Advert:index.html.twig';
        $this->agg_type = array(
            'parent' => AggregateFilter::TYPE_ADVERT,
            'table' => AggregateFilter::TYPE_ADVERT_CHILDREN,
            'daily' => AggregateFilter::TYPE_DAILY_ADVERT,
            'hourly' => AggregateFilter::TYPE_HOURLY_ADVERT
        );

        $this->parent_repo = 'OnCallAdminBundle:AdGroup';
        $this->child_repo = 'OnCallAdminBundle:Advert';
        $this->child_fetch_method = 'getAdverts';

        $this->url_child = '';
        $this->url_parent = 'oncall_admin_adverts';
    }

    public function indexAction($id)
    {
        // get main data
        $data = $this->fetchMainData($id);

        // get client id
        $cid = $data['parent']->getCampaign()->getClient()->getID();

        // get the numbers
        $dql = 'select n from OnCallAdminBundle:Number n left outer join n.advert a where n.client_id = :client_id and a is null';
        $query = $this->getDoctrine()
            ->getManager()
            ->createQuery($dql)
            ->setParameter('client_id', $cid);
        $numbers = $query->getResult();

        // fill new parameters
        $data['numbers'] = $numbers;

        return  $this->render($this->template, $data);
    }

    protected function getAvailableNumbers($client_id)
    {

    }
}
