<?php

namespace OnCall\Bundle\AdminBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class Client extends EntityRepository
{
    public function findFirst($user_id)
    {
        try
        {
            $dql = 'select c from OnCallAdminBundle:Client c where c.user_id = :user_id';
            $query = $this->getEntityManager()
                ->createQuery($dql)
                ->setParameter('user_id', $user_id)
                ->setMaxResults(1);
            return $query->getSingleResult();
        }
        catch (NoResultException $e)
        {
            return null;
        }
    }
}
