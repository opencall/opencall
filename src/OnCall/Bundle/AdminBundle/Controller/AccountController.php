<?php

namespace OnCall\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use OnCall\Bundle\AdminBundle\Model\MenuHandler;
use Symfony\Component\HttpFoundation\Response;

class AccountController extends Controller
{
    public function indexAction()
    {
        $dql = 'select u from OnCall\Bundle\UserBundle\Entity\User u where u.roles = :role';
        $query = $this->getDoctrine()
            ->getManager()
            ->createQuery($dql)
            ->setParameter('role', 'a:0:{}');
        $accounts = $query->getResult();


        return $this->render(
            'OnCallAdminBundle:Account:index.html.twig',
            array(
                'sidebar_menu' => MenuHandler::getMenu('account'),
                'accounts' => $accounts
            )
        );

    }

    public function createAction()
    {
        $mgr = $this->get('fos_user.user_manager');
        $user = $mgr->createUser();

        $req = $this->getRequest();
        /*
        $form = $this->createFormBuilder($user)
            ->add('multi_client', 'checkbox')
            ->add('username', 'text')
            ->add('password', 'password')
            ->add('name', 'text')
            ->add('email', 'text')
            ->add('business_name', 'text')
            ->add('phone', 'text')
            ->add('address', 'text')
            ->add('bill_business_name', 'text')
            ->add('bill_name', 'text')
            ->add('bill_email', 'text')
            ->add('bill_phone', 'text')
            ->add('bill_address', 'text')
            ->add('enabled', 'choice', array(
                'choices' => array(
                    '1' => 'Active',
                    '0' => 'Disabled'
                )
            ))
            ->getForm();

        $form->handleRequest($req);
        */

        $data = $req->request->all();
        $this->updateUser($user, $data);
        $mgr->updateUser($user);
        return $this->redirect($this->generateUrl('oncall_admin_accounts'));
    }

    protected function updateUser($user, $data)
    {
        $user->setUsername($data['username'])
            ->setPlainPassword($data['password'])
            ->setName($data['name'])
            ->setEmail($data['email'])
            ->setBusinessName($data['business_name'])
            ->setPhone($data['phone'])
            ->setAddress($data['address'])
            ->setBillBusinessName($data['bill_business_name'])
            ->setBillName($data['bill_name'])
            ->setBillEmail($data['bill_email'])
            ->setBillPhone($data['bill_phone'])
            ->setBillAddress($data['bill_address'])
            ->setEnabled($data['enabled'])
            ->setRoles(array('ROLE_USER'));

        // check if multi-client
        if (isset($data['multi_client']) && $data['multi_client'] == 1)
            $user->setMultiClient(true);
        else
            $user->setMultiCilent(false);
    }

    public function getAction($id)
    {
        $mgr = $this->get('fos_user.user_manager');
        $edit_user = $mgr->findUserBy(array('id' => $id));

        return new Response($edit_user->jsonify());
    }

    public function updateAction($id)
    {
        $mgr = $this->get('fos_user.user_manager');
        $edit_user = $mgr->findUserBy(array('id' => $id));

        $data = $this->getRequest()->request->all();
        $this->updateUser($edit_user, $data);
        $mgr->updateUser($edit_user);
        return $this->redirect($this->generateUrl('oncall_admin_accounts'));
    }
}
