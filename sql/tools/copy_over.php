<?php

// parameters
$old_username = 'demo';
$new_username = 'docdoc';
$new_name = 'DocDoc';

// setup pdo
$dsn = 'mysql:host=localhost;dbname=oncall';
$user = 'root';
$pass = '';
$pdo = new PDO($dsn, $user, $pass);

$copier = new Copier($pdo);
$copier->copy($old_username, $new_username);

class Copier
{
    protected $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function copy($old_username, $new_username)
    {
        // user
        $user_row = $this->getUser($old_username);
        $old_user_id = $user_row['id'];
        $new_user_id = $this->storeUser($user_row, $new_username);

        // account counter
        $ac_rows = $this->getAccountCounters($old_user_id);
        $this->storeAccountCounters($ac_rows, $new_user_id);

        // clients
        $client_rows = $this->getClients($old_user_id);
        $this->storeClients($client_rows, $new_user_id);
    }

    protected function getUser($username)
    {
        $sql = 'select * from User where username=:username';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':username', $username);
        if (!$stmt->execute())
            return null;

        $row = $stmt->fetch();
        if (!$row)
            return null;

        return $row;
    }

    protected function storeUser($row, $username)
    {
        $sql = 'insert into User (username, username_canonical, email, email_canonical, enabled, salt, password, last_login, locked, expired, expires_at, confirmation_token, password_requested_at, roles, credentials_expired, credentials_expire_at, multi_client, name, business_name, phone, address, bill_business_name, bill_name, bill_email, bill_phone, bill_address, date_create) values (:username, :username_canonical, :email, :email_canonical, :enabled, :salt, :password, :last_login, :locked, :expired, :expires_at, :confirmation_token, :password_requested_at, :roles, :credentials_expired, :credentials_expire_at, :multi_client, :name, :business_name, :phone, :address, :bill_business_name, :bill_name, :bill_email, :bill_phone, :bill_address, :date_create)';
        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':username_canonical', $username);
        $stmt->bindParam(':email', $row['email']);
        $stmt->bindParam(':email_canonical', $row['email_canonical']);
        $stmt->bindParam(':enabled', $row['enabled']);
        $stmt->bindParam(':salt', $row['salt']);
        $stmt->bindParam(':password', $row['password']);
        $stmt->bindParam(':last_login', $row['last_login']);
        $stmt->bindParam(':locked', $row['locked']);
        $stmt->bindParam(':expired', $row['expired']);
        $stmt->bindParam(':expires_at', $row['expires_at']);
        $stmt->bindParam(':confirmation_token', $row['confirmation_token']);
        $stmt->bindParam(':password_requested_at', $row['password_requested_at']);
        $stmt->bindParam(':roles', $row['roles']);
        $stmt->bindParam(':credentials_expired', $row['credentials_expired']);
        $stmt->bindParam(':credentials_expire_at', $row['credentials_expire_at']);
        $stmt->bindParam(':multi_client', $row['multi_client']);
        $stmt->bindParam(':name', $row['name']);
        $stmt->bindParam(':business_name', $row['business_name']);
        $stmt->bindParam(':phone', $row['phone']);
        $stmt->bindParam(':address', $row['address']);
        $stmt->bindParam(':bill_business_name', $row['bill_business_name']);
        $stmt->bindParam(':bill_name', $row['bill_name']);
        $stmt->bindParam(':bill_email', $row['bill_email']);
        $stmt->bindParam(':bill_phone', $row['bill_phone']);
        $stmt->bindParam(':bill_address', $row['bill_address']);
        $stmt->bindParam(':date_create', $row['date_create']);

        $stmt->execute();

        return $this->pdo->lastInsertId();
    }

    protected function getAccountCounters($user_id)
    {
        $sql = 'select * from AccountCounter where user_id=:user_id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        if (!$stmt->execute())
            return null;

        return $stmt->fetchAll();
    }

    protected function storeAccountCounters($rows, $new_user_id)
    {
        $sql = 'insert into AccountCounter (date_in, user_id, client_count, number_count, call_count, duration) values (:date_in, :user_id, :client_count, :number_count, :call_count, :duration)';
        $stmt = $this->pdo->prepare($sql);
        foreach ($rows as $row)
        {
            $stmt->bindParam(':date_in', $row['date_in']);
            $stmt->bindParam(':user_id', $new_user_id);
            $stmt->bindParam(':client_count', $row['client_count']);
            $stmt->bindParam(':number_count', $row['number_count']);
            $stmt->bindParam(':call_count', $row['call_count']);
            $stmt->bindParam(':duration', $row['duration']);

            $stmt->execute();
        }
    }

    protected function getClients($user_id)
    {
        $sql = 'select * from Client where user_id=:user_id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        if (!$stmt->execute())
            return null;

        return $stmt->fetchAll();
    }

    protected function storeClients($rows, $new_user_id)
    {
        $sql = 'insert into Client (user_id, name, timezone, status, date_create, call_count, duration) values (:user_id, :name, :timezone, :status, :date_create, :call_count, :duration)';
        $stmt = $this->pdo->prepare($sql);
        foreach ($rows as $row)
        {
            $stmt->bindParam(':user_id', $new_user_id);
            $stmt->bindParam(':name', $row['name']);
            $stmt->bindParam(':timezone', $row['timezone']);
            $stmt->bindParam(':status', $row['status']);
            $stmt->bindParam(':date_create', $row['date_create']);
            $stmt->bindParam(':call_count', $row['call_count']);
            $stmt->bindParam(':duration', $row['duration']);

            $stmt->execute();

            $new_client_id = $this->pdo->lastInsertId();
            $client_id = $row['id'];

            // get and save counters
            $count_rows = $this->getCounters($client_id);
            $this->storeCounters($count_rows, $new_client_id);

            // get and save call log
            $log_rows = $this->getCallLogs($client_id);
            $this->storeCallLogs($log_rows, $new_client_id);

            // campaigns
            $camp_rows = $this->getCampaigns($client_id);
            $this->storeCampaigns($camp_rows, $new_client_id);
        }

    }

    protected function updateLog($field, $client_id, $old_val, $new_val)
    {
        $sql = "update CallLog set $field = :new_val where client_id = :client_id and $field = :old_val";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':client_id', $client_id);
        $stmt->bindParam(':new_val', $new_val);
        $stmt->bindParam(':old_val', $old_val);

        return $stmt->execute();
    }

    protected function updateCounter($field, $client_id, $old_val, $new_val)
    {
        $sql = "update Counter set $field = :new_val where client_id = :client_id and $field = :old_val";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':client_id', $client_id);
        $stmt->bindParam(':new_val', $new_val);
        $stmt->bindParam(':old_val', $old_val);

        return $stmt->execute();
    }

    protected function getCampaigns($client_id)
    {
        $sql = 'select * from Campaign where client_id=:client_id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':client_id', $client_id);
        if (!$stmt->execute())
            return null;

        return $stmt->fetchAll();
    }

    protected function storeCampaigns($rows, $client_id)
    {
        $sql = 'insert into Campaign (client_id, name, status, date_create) values (:client_id, :name, :status, :date_create)';
        $stmt = $this->pdo->prepare($sql);
        foreach ($rows as $row)
        {
            $stmt->bindParam(':client_id', $client_id);
            $stmt->bindParam(':name', $row['name']);
            $stmt->bindParam(':status', $row['status']);
            $stmt->bindParam(':date_create', $row['date_create']);

            $stmt->execute();

            $old_camp_id = $row['id'];
            $new_camp_id = $this->pdo->lastInsertId();

            // counters and logs
            $this->updateCounter('campaign_id', $client_id, $old_camp_id, $new_camp_id);
            $this->updateLog('campaign_id', $client_id, $old_camp_id, $new_camp_id);

            // ad groups
            $adg_rows = $this->getAdGroups($old_camp_id);
            $this->storeAdGroups($adg_rows, $new_camp_id, $client_id);
        }
    }

    protected function getAdGroups($camp_id)
    {
        $sql = 'select * from AdGroup where campaign_id=:camp_id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':camp_id', $camp_id);
        if (!$stmt->execute())
            return null;

        return $stmt->fetchAll();
    }

    protected function storeAdGroups($rows, $camp_id, $client_id)
    {
        $sql = 'insert into AdGroup (campaign_id, name, status, date_create) values (:camp_id, :name, :status, :date_create)';
        $stmt = $this->pdo->prepare($sql);
        foreach ($rows as $row)
        {
            $stmt->bindParam(':camp_id', $camp_id);
            $stmt->bindParam(':name', $row['name']);
            $stmt->bindParam(':status', $row['status']);
            $stmt->bindParam(':date_create', $row['date_create']);

            $stmt->execute();

            $old_adg_id = $row['id'];
            $new_adg_id = $this->pdo->lastInsertId();

            // counters and logs
            $this->updateCounter('adgroup_id', $client_id, $old_adg_id, $new_adg_id);
            $this->updateLog('adgroup_id', $client_id, $old_adg_id, $new_adg_id);

            // advert
            $ad_rows = $this->getAdverts($old_adg_id);
            $this->storeAdverts($ad_rows, $new_adg_id, $client_id);
        }
    }

    protected function getAdverts($adg_id)
    {
        $sql = 'select * from Advert where adgroup_id=:adg_id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':adg_id', $adg_id);
        if (!$stmt->execute())
            return null;

        return $stmt->fetchAll();
    }

    protected function storeAdverts($rows, $adg_id, $client_id)
    {
        $sql = 'insert into Advert (adgroup_id, name, xml_replace, xml_override, status, date_create, record, speak, speak_message) values (:adg_id, :name, :xml_replace, :xml_override, :status, :date_create, :record, :speak, :speak_message)';
        $stmt = $this->pdo->prepare($sql);
        foreach ($rows as $row)
        {
            $stmt->bindParam(':adg_id', $adg_id);
            $stmt->bindParam(':name', $row['name']);
            $stmt->bindParam(':xml_replace', $row['xml_replace']);
            $stmt->bindParam(':xml_override', $row['xml_override']);
            $stmt->bindParam(':status', $row['status']);
            $stmt->bindParam(':date_create', $row['date_create']);
            $stmt->bindParam(':record', $row['record']);
            $stmt->bindParam(':speak', $row['speak']);
            $stmt->bindParam(':speak_message', $row['speak_message']);

            $stmt->execute();

            $old_ad_id = $row['id'];
            $new_ad_id = $this->pdo->lastInsertId();

            // counters and logs
            $this->updateCounter('advert_id', $client_id, $old_ad_id, $new_ad_id);
            $this->updateLog('advert_id', $client_id, $old_ad_id, $new_ad_id);
        }
    }

    protected function getCounters($client_id)
    {
        $sql = 'select * from Counter where client_id=:client_id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':client_id', $client_id);
        if (!$stmt->execute())
            return null;

        return $stmt->fetchAll();
    }

    protected function storeCounters($rows, $client_id)
    {
        $sql = 'insert into Counter (date_in, client_id, campaign_id, adgroup_id, advert_id, number_id, caller_id, count_total, count_plead, count_failed, duration_secs) values (:date_in, :client_id, :campaign_id, :adgroup_id, :advert_id, :number_id, :caller_id, :count_total, :count_plead, :count_failed, :duration_secs)';
        $stmt = $this->pdo->prepare($sql);
        foreach ($rows as $row)
        {
            $stmt->bindParam(':date_in', $row['date_in']);
            $stmt->bindParam(':client_id', $client_id);
            $stmt->bindParam(':campaign_id', $row['campaign_id']);
            $stmt->bindParam(':adgroup_id', $row['adgroup_id']);
            $stmt->bindParam(':advert_id', $row['advert_id']);
            $stmt->bindParam(':number_id', $row['number_id']);
            $stmt->bindParam(':caller_id', $row['caller_id']);
            $stmt->bindParam(':count_total', $row['count_total']);
            $stmt->bindParam(':count_plead', $row['count_plead']);
            $stmt->bindParam(':count_failed', $row['count_failed']);
            $stmt->bindParam(':duration_secs', $row['duration_secs']);

            $stmt->execute();
        }
    }

    protected function getCallLogs($client_id)
    {
        $sql = 'select * from CallLog where client_id=:client_id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':client_id', $client_id);
        if (!$stmt->execute())
            return null;

        return $stmt->fetchAll();
    }

    protected function storeCallLogs($rows, $client_id)
    {
        $sql = 'insert into CallLog (date_in, call_id, origin_number, dialled_number, destination_number, date_start, date_end, duration, bill_duration, bill_rate, status, hangup_cause, advert_id, adgroup_id, campaign_id, client_id, b_status, b_hangup_cause, audio_record) values (:date_in, :call_id, :origin_number, :dialled_number, :destination_number, :date_start, :date_end, :duration, :bill_duration, :bill_rate, :status, :hangup_cause, :advert_id, :adgroup_id, :campaign_id, :client_id, :b_status, :b_hangup_cause, :audio_record)';
        $stmt = $this->pdo->prepare($sql);
        foreach ($rows as $row)
        {
            $stmt->bindParam(':date_in', $row['date_in']);
            $stmt->bindParam(':call_id', $row['call_id']);
            $stmt->bindParam(':origin_number', $row['origin_number']);
            $stmt->bindParam(':dialled_number', $row['dialled_number']);
            $stmt->bindParam(':destination_number', $row['destination_number']);
            $stmt->bindParam(':date_start', $row['date_start']);
            $stmt->bindParam(':date_end', $row['date_end']);
            $stmt->bindParam(':duration', $row['duration']);
            $stmt->bindParam(':bill_duration', $row['bill_duration']);
            $stmt->bindParam(':bill_rate', $row['bill_rate']);
            $stmt->bindParam(':status', $row['status']);
            $stmt->bindParam(':hangup_cause', $row['hangup_cause']);
            $stmt->bindParam(':advert_id', $row['advert_id']);
            $stmt->bindParam(':adgroup_id', $row['adgroup_id']);
            $stmt->bindParam(':campaign_id', $row['campaign_id']);
            $stmt->bindParam(':client_id', $client_id);
            $stmt->bindParam(':b_status', $row['b_status']);
            $stmt->bindParam(':b_hangup_cause', $row['b_hangup_cause']);
            $stmt->bindParam(':audio_record', $row['audio_record']);

            $stmt->execute();
        }
    }
}
