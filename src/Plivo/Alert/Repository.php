<?php

namespace Plivo\Alert;

use PDO;

class Repository
{
    protected $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    protected function createEntry($row)
    {
        $entry = new Entry();
        $entry->setClientID($row['id'])
            ->setEnabled($row['alert_enable'])
            ->setEmail($row['alert_email'])
            ->setCampaignID($row['alert_cid'])
            ->setAdGroupID($row['alert_adgid'])
            ->setAdvertID($row['alert_adid']);

        return $entry;
    }

    public function find($client_id)
    {
        $sql = 'select * from Client where id = :client_id limit 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':client_id', $client_id);

        if (!$stmt->execute())
            return null;

        $row = $stmt->fetch();
        if (!$row)
            return null;

        return $this->createEntry($row);
    }
}
