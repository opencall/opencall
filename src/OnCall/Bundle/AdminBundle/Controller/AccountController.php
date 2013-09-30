<?php

namespace OnCall\Bundle\AdminBundle\Controller;

use OnCall\Bundle\AdminBundle\Model\MenuHandler;
use Symfony\Component\HttpFoundation\Response;
use OnCall\Bundle\AdminBundle\Entity\User;
use OnCall\Bundle\AdminBundle\Entity\Client;
use OnCall\Bundle\AdminBundle\Model\Controller;
use Doctrine\DBAL\DBALException;


class AccountController extends Controller
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

        // get role hash for menu
        $user = $this->getUser();
        $role_hash = $user->getRoleHash();
        $alerts = array();

        return $this->render(
            'OnCallAdminBundle:Account:index.html.twig',
            array(
                'sidebar_menu' => MenuHandler::getMenu($role_hash, 'account'),
                'alerts' => $alerts,
                'accounts' => $accounts
            )
        );

    }

    public function createAction()
    {
        // add user
        try 
        {
            $mgr = $this->get('fos_user.user_manager');
            $user = $mgr->createUser();

            $data = $this->getRequest()->request->all();
            $this->updateUser($user, $data);
            $mgr->updateUser($user);
        }
        catch (DBALException $e)
        {
            $this->addFlash('error', 'Could not create account, username probably exists.');
        }

        try
        {
            $em = $this->getDoctrine()->getManager();

            // add default client for non-multi-client
            if (!$user->isMultiClient())
            {
                $client = new Client();
                $client->setUser($user)
                    ->setName($user->getName())
                    ->setTimezone('8.0');
                $em->persist($client);
                $em->flush();
            }
        }
        catch (DBALException $e)
        {
            $this->addFlash('error', 'Could not add default client for account.');
        }

        return $this->redirect($this->generateUrl('oncall_admin_accounts'));
    }

    protected function updateUser(User $user, $data)
    {
        $user->setName($data['name'])
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

        // username is not set on edit
        if (isset($data['username']) && !empty($data['username']))
            $user->setUsername($data['username']);

        // check if password was specified
        if (!empty($data['password']))
            $user->setPlainPassword($data['password']);

        // check if multi-client
        if (isset($data['multi_client']) && $data['multi_client'] == 1)
            $user->setMultiClient(true);
        else
            $user->setMultiClient(false);
    }

    public function getAction($id)
    {
        $mgr = $this->get('fos_user.user_manager');
        $edit_user = $mgr->findUserBy(array('id' => $id));

        return new Response($edit_user->jsonify());
    }

    public function updateAction($id)
    {
        // find user
        $mgr = $this->get('fos_user.user_manager');
        $edit_user = $mgr->findUserBy(array('id' => $id));

        $data = $this->getRequest()->request->all();

        // update user data and persist
        $this->updateUser($edit_user, $data);
        $mgr->updateUser($edit_user);

        $this->addFlash('success', 'Account ' . $edit_user->getUsername() . ' updated.');

        return $this->redirect($this->generateUrl('oncall_admin_accounts'));
    }

    public function passwordFormAction()
    {
        $user = $this->getUser();
        $role_hash = $user->getRoleHash();

        return $this->render(
            'OnCallAdminBundle:Account:password.html.twig',
             array(
                'sidebar_menu' => MenuHandler::getMenu($role_hash),
                'user' => $user
            )
        );
    }

    public function passwordSubmitAction()
    {
        $data = $this->getRequest()->request->all();

        // field check
        if (!isset($data['pass1']) || !isset($data['pass2']) || empty($data['pass1']) || empty($data['pass2']))
        {
            $this->addFlash('error', 'Password cannot be blank.');
            return $this->redirect($this->generateUrl('oncall_admin_password_form'));
        }

        // match check
        if ($data['pass1'] != $data['pass2'])
        {
            $this->addFlash('error', 'Passwords do not match.');
            return $this->redirect($this->generateUrl('oncall_admin_password_form'));
        }

        // change password
        $user = $this->getUser();
        $user->setPlainPassword($data['pass1']);
        $mgr = $this->get('fos_user.user_manager');
        $mgr->updateUser($user);

        $this->addFlash('success', 'Password changed successfully.');

        return $this->redirect($this->generateUrl('oncall_admin_password_form'));
    }
}
