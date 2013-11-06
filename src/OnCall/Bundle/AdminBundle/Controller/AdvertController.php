<?php

namespace OnCall\Bundle\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use OnCall\Bundle\AdminBundle\Model\ItemController;
use OnCall\Bundle\AdminBundle\Model\AggregateFilter;
use OnCall\Bundle\AdminBundle\Entity\Item;
use OnCall\Bundle\AdminBundle\Entity\Number;
use Plivo\AccountCounter\Repository as ACRepo;
use Plivo\AccountCounter\Entry as ACEntry;
use Plivo\NumberFormatter;
use DateTime;

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

        $this->child_id_field = 'adgroup_id';
        $this->child_filter_var = 'adid';
    }

    public function indexAction($id)
    {
        // get main data
        $data = $this->fetchMainData($id);

        // get client id and campaign id
        $campaign = $data['parent']->getCampaign();
        $cid = $campaign->getClient()->getID();
        $camp_id = $campaign->getID();

        // log url
        $agg_filter = $data['agg_filter'];
        $date_from = $agg_filter->getDateFrom();
        $date_to = $agg_filter->getDateTo();
        $this->log_url = '/client/' . $cid . '/call_log?cid=' . $camp_id . '&agid=' . $id;
        $this->log_url .= '&dts=' . $date_from->format('Y-m-d') . '&dte=' . $date_to->format('Y-m-d');

        $data['log_url'] = $this->log_url;

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
            {
                $nf = new NumberFormatter();
                $advert->setDestination($nf->clean($data['destination']));
            }
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
