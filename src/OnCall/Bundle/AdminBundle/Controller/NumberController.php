<?php

namespace OnCall\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use OnCall\Bundle\AdminBundle\Model\MenuHandler;
use Symfony\Component\HttpFoundation\Response;
use OnCall\Bundle\AdminBundle\Entity\Number;

class NumberController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        // get accounts (all users who have no roles (ROLE_USER)
        $dql = 'select u from OnCall\Bundle\AdminBundle\Entity\User u where u.roles = :role';
        $acc_query = $em->createQuery($dql)
            ->setParameter('role', 'a:0:{}');
        $accounts = $acc_query->getResult();

        // get numbers
        // TODO: type and usage filter
        $dql = 'select n from OnCall\Bundle\AdminBundle\Entity\Number n order by n.number';
        $num_query = $em->createQuery($dql);
        $numbers = $num_query->getResult();

        // get role hash for menu
        $user = $this->getUser();
        $role_hash = $user->getRoleHash();

        return $this->render(
            'OnCallAdminBundle:Number:index.html.twig',
            array(
                'sidebar_menu' => MenuHandler::getMenu($role_hash, 'number'),
                'accounts' => $accounts,
                'numbers' => $numbers
            )
        );
    }

    public function createMultipleAction()
    {
        $data = $this->getRequest()->request->all();
        $em = $this->getDoctrine()->getManager();

        // get numbers
        $numbers = explode("\n", $data['numbers']);
        $nlen = count($numbers);

        // trim numbers
        // TODO: check if numbers already exist
        for ($i = 0; $i < $nlen; $i++)
            $numbers[$i] = trim($numbers[$i]);

        // TODO: cleanup parameters / default value
        $provider = trim($data['provider']);
        $type = $data['type'];
        $price_buy = $data['price_buy'];
        $price_resale = $data['price_resale'];

        // create the numbers
        foreach ($numbers as $num_text)
        {
            $num = new Number();
            $num->setNumber($num_text)
                ->setProvider($provider)
                ->setType($type)
                ->setPriceBuy($price_buy)
                ->setPriceResale($price_resale);
            $em->persist($num);
        }
        $em->flush();

        return $this->redirect($this->generateUrl('oncall_admin_numbers'));
        
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
