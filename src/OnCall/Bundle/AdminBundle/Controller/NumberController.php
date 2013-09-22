<?php

namespace OnCall\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use OnCall\Bundle\AdminBundle\Model\MenuHandler;
use Symfony\Component\HttpFoundation\Response;

class NumberController extends Controller
{
    public function indexAction()
    {
        // get accounts (all users who have no roles (ROLE_USER)
        $dql = 'select u from OnCall\Bundle\AdminBundle\Entity\User u where u.roles = :role';
        $query = $this->getDoctrine()
            ->getManager()
            ->createQuery($dql)
            ->setParameter('role', 'a:0:{}');
        $accounts = $query->getResult();

        // TODO: type filter

        // get role hash for menu
        $user = $this->getUser();
        $role_hash = $user->getRoleHash();

        return $this->render(
            'OnCallAdminBundle:Number:index.html.twig',
            array(
                'sidebar_menu' => MenuHandler::getMenu($role_hash, 'number'),
                'accounts' => $accounts
            )
        );
    }

    public function createMultipleAction()
    {
    }

    public function getAction()
    {
    }

    public function updateAction()
    {
    }

    public function assignAction()
    {
    }

    public function deleteAction()
    {
    }
}
