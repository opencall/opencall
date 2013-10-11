<?php

namespace OnCall\Bundle\AdminBundle\Repositories;

use Doctrine\ORM\EntityRepository;

class CallLog extends EntityRepository
{
    public function findLatest($filter = array(), $limit = 100, $offset = 0)
    {
        return $this->findBy(
            $filter,
            array('id' => 'DESC'),
            $limit,
            $offset
        );
    }
}
