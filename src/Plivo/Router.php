<?php

namespace Plivo;

use OnCall\Bundle\AdminBundle\Model\ItemStatus;

class Router
{
    protected $pdo;
    protected $num_data;
    protected $callback_url;
    protected $record_url;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->num_data = null;
        $this->callback_url = null;
        $this->record_url = null;
    }

    public function setCallbackURL($url)
    {
        $this->callback_url = $url;
        return $this;
    }

    public function setRecordURL($url)
    {
        $this->record_url = $url;
        return $this;
    }

    public function resolve(Parameters $params)
    {
        $num = $params->getTo();
        $res = $this->checkNumber($num);

        // create resposne
        $resp = new Response();

        // check if any numbers reflected correctly
        if (!$res)
        {
            $act_params = array();
            $act_params['language'] = 'en-GB';
            $act_params['text'] = 'This number does not have a valid destination. Please contact CallTracking for more information.';

            $resp->addAction(new Action(Action::TYPE_SPEAK, $act_params));

            return $resp;
        }

        $this->num_data = $res;

        // check if it has custom xml
        if ($res['xml_override'])
        {
            $act_params = array();
            $xml = $res['xml_replace'];
            $act_params['xml'] = $xml;
            $action = new Action(Action::TYPE_CUSTOM_XML, $act_params);
            $resp->addAction($action);

            return $resp;
        }

        // TODO: check if we have speak

        // check if we have record
        if ($res['record'])
        {
            $act_params = array();
            $act_params['record_url'] = $this->record_url;
            $action = new Action(Action::TYPE_RECORD, $act_params);
            $resp->addAction($action);
        }

        // dial action
        $act_params = array();
        $dest = $res['destination'];
        $act_params['number'] = $dest;

        // set caller as caller_id to redirected call
        if ($params->getFrom() != null)
            $act_params['caller_id'] = $params->getFrom();

        // set caller callback url
        if ($this->callback_url != null)
            $act_params['callback_url'] = $this->callback_url;

        // $act_params['caller_id'] = $params->getTo();
        $action = new Action(Action::TYPE_DIAL, $act_params);
        $resp->addAction($action);

        return $resp;
    }

    public function getNumberData()
    {
        return $this->num_data;
    }

    protected function checkNumber($num)
    {
        $status = ItemStatus::ACTIVE;
        $sql = 'select *,Advert.id as advert_id from Number,Advert,AdGroup,Campaign,Client
            where Number.id = :num_id
            and Advert.number_id = Number.id
            and Advert.adgroup_id = AdGroup.id
            and AdGroup.campaign_id = Campaign.id
            and Campaign.client_id = Client.id
            and Advert.status = :status
            and AdGroup.status = :status
            and Campaign.status = :status
            and Client.status = :status';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':num_id', $num);
        $stmt->bindParam(':status', $status);
        if (!$stmt->execute())
            return false;
            // throw new Exception('Database problem encountered.');

        $row = $stmt->fetch();
        if (!$row)
            return false;

        return $row;
    }
}
