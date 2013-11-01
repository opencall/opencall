<?php

namespace Plivo\AccountCounter;

use DateTime;
use PDO;

class Repository
{
    protected $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function initializeAll(DateTime $date)
    {
        $sql = 'select * from User';
        $stmt = $this->pdo->prepare($sql);

        if (!$stmt->execute())
            return $this;

        while ($row = $stmt->fetch())
            $this->initialize($date, $row['id']);

        return $this;
    }

    public function initialize(DateTime $date, $user_id)
    {
        $entry = $this->fetch($date, $user_id);

        if ($entry != null)
            return $this;

        // get active client count
        $client_count = $this->getClientCount($user_id);

        // get number count
        $num_count = $this->getNumberCount($user_id);

        // get call count and duration
        $calldur = $this->getCallCountAndDuration($date, $user_id);

        // insert
        $sql = 'insert into AccountCounter (date_in, user_id, client_count, number_count, call_count, duration) values (:date, :user_id, :client_count, :number_count, :call_count, :duration)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $user_id);
        $stmt->bindValue(':date', $date->format('Y-m-d'));
        $stmt->bindValue(':client_count', $client_count);
        $stmt->bindValue(':number_count', $num_count);
        $stmt->bindValue(':call_count', $calldur['call']);
        $stmt->bindValue(':duration', $calldur['duration']);

        $stmt->execute();

        return $this;
    }

    protected function getCallCountAndDuration(DateTime $date, $user_id)
    {
        $date_from = $date->format('Y-m-d') . ' 00:00:00';
        $date_to = $date->format('Y-m-d') . ' 23:59:59';
        $sql = 'select sum(count_total) as agg_call, sum(duration_secs) as agg_dur from Counter,Client where Counter.client_id = Client.id and Client.user_id = :user_id and Client.status = 1 and Counter.date_in > :date_from and Counter.date_in < :date_to';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $user_id);
        $stmt->bindValue(':date_from', $date_from);
        $stmt->bindValue(':date_to', $date_to);

        if (!$stmt->execute())
            return array('call' => 0, 'duration' => 0);

        $row = $stmt->fetch();
        if (!$row)
            return array('call' => 0, 'duration' => 0);

        if ($row['agg_call'] == null)
            $row['agg_call'] = 0;
        if ($row['agg_dur'] == null)
            $row['agg_dur'] = 0;

        return array(
            'call' => $row['agg_call'],
            'duration' => $row['agg_dur']
        );
    }

    protected function getNumberCount($user_id)
    {
        $sql = 'select count(*) as num_count from Number,Client where Number.client_id = Client.id and Client.status = 1 and Client.user_id = :user_id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);

        if (!$stmt->execute())
            return 0;

        $row = $stmt->fetch();
        if (!$row)
            return 0;

        return $row['num_count'];
    }

    protected function getClientCount($user_id)
    {
        $sql = 'select count(*) as client_count from Client where user_id = :user_id and status = 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);

        if (!$stmt->execute())
            return 0;

        $row = $stmt->fetch();
        if (!$row)
            return 0;

        return $row['client_count'];
    }

    public function fetchAll()
    {
        $sql = 'select * from AccountCounter order by date_in desc';
        $stmt = $this->pdo->prepare($sql);

        if (!$stmt->execute())
            return array();

        $entries = array();
        while ($row = $stmt->fetch())
        {
            $entry = new Entry(new DateTime($row['date_in']), $row['user_id']);
            $entry->setClient($row['client_count'])
                ->setNumber($row['number_count'])
                ->setCall($row['call_count'])
                ->setDuration($row['duration']);

            $entries[] = $entry;
        }

        return $entries;
    }

    public function fetch(DateTime $date, $user_id)
    {
        // check if we already have something initialized
        $sql = 'select * from AccountCounter where date_in = :date_in and user_id = :user_id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':date_in', $date->format('Y-m-d'));
        $stmt->bindValue(':user_id', $user_id);

        if (!$stmt->execute())
            return null;

        $row = $stmt->fetch();
        if (!$row)
            return null;

        $entry = new Entry($date, $user_id);
        $entry->setClient($row['client_count'])
            ->setNumber($row['number_count'])
            ->setCall($row['call_count'])
            ->setDuration($row['duration']);

        return $entry;
    }

    public function append(Entry $entry)
    {
        $sql = 'update AccountCounter set client_count = client_count + :client_count, number_count = number_count + :number_count, call_count = call_count + :call_count, duration = duration + :duration where date_in = :date_in and user_id = :user_id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':client_count', $entry->getClient());
        $stmt->bindValue(':number_count', $entry->getNumber());
        $stmt->bindValue(':call_count', $entry->getCall());
        $stmt->bindValue(':duration', $entry->getDuration());
        $stmt->bindValue(':date_in', $entry->getDateIn()->format('Y-m-d'));
        $stmt->bindValue(':user_id', $entry->getUserID());

        return $stmt->execute();
    }
}
