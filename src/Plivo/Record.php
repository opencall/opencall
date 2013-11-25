<?php

namespace Plivo;

use Plivo\Log\Repository as LogRepository;
use PDO;

class Record
{
    protected $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function run($post)
    {
        // get params
        $call_id = $post['CallUUID'];
        $audio_url = $post['RecordUrl'];

        // update log
        $log_repo = new LogRepository($this->pdo);
        $log_repo->updateRecord($call_id, $audio_url);
    }
}
