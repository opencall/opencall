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

    public function findAllWithUsers()
    {
        try
        {
            // get clients, eager load users
            $dql = 'select c,u from OnCallAdminBundle:Client c join c.user u order by u.business_name asc, c.name asc';
            $query = $this->getEntityManager()
                ->createQuery($dql);
            return $query->getResult();
        }
        catch (NoResultException $e)
        {
            return array();
        }
    }
}
