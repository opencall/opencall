<?php

namespace OnCall\Bundle\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use OnCall\Bundle\AdminBundle\Model\ItemController;
use OnCall\Bundle\AdminBundle\Model\AggregateFilter;
use OnCall\Bundle\AdminBundle\Entity\Item;
use OnCall\Bundle\AdminBundle\Entity\Number;

class AdvertController extends ItemController
{
    public function __construct()
    {
        $this->name = 'Advert';
        $this->top_color = 'yellow';
        $this->template = 'OnCallAdminBundle:Advert:index.html.twig';
        $this->agg_type = array(
            'parent' => AggregateFilter::TYPE_ADGROUP,
            'table' => AggregateFilter::TYPE_ADGROUP_CHILDREN,
            'daily' => AggregateFilter::TYPE_DAILY_ADGROUP,
            'hourly' => AggregateFilter::TYPE_HOURLY_ADGROUP
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

        // fill new parameters
        $data['numbers'] = $this->getAvailableNumbers($cid);

        return  $this->render($this->template, $data);
    }

    protected function getAvailableNumbers($client_id)
    {
        // get the numbers
        $dql = 'select n from OnCallAdminBundle:Number n left outer join n.advert a where n.client_id = :client_id and a is null';
        $query = $this->getDoctrine()
            ->getManager()
            ->createQuery($dql)
            ->setParameter('client_id', $client_id);

        return $query->getResult();
    }

    protected function update(Item $advert, $data)
    {
        parent::update($advert, $data);

        // check number
        if (isset($data['number']))
        {
            // if blank number
            if ($data['number'] == 0)
                $num = null;
            else
            {
                // find number
                $num = $this->getDoctrine()
                    ->getRepository('OnCallAdminBundle:Number')
                    ->find(trim($data['number']));
            }

            $advert->setNumber($num);
        }

        if (isset($data['destination']))
        {
            if ($data['destination'] == 0)
                $advert->setDestination(null);
            else
                $advert->setDestination($data['destination']);
        }
        else
            $advert->setDestination(null);

        // check xml stuff only if admin
        if ($this->get('security.context')->isGranted('ROLE_PREVIOUS_ADMIN'))
        {
            if (isset($data['xml_replace']))
                $advert->setXMLReplace($data['xml_replace']);

            if (isset($data['xml_override']))
                $advert->setXMLOverride(1);
            else
                $advert->setXMLOverride(0);
        }
    }
}
