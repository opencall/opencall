<?php

namespace OnCall\Bundle\AdminBundle\Model;

class MenuHandler
{
    static public function getMenu($role_hash, $active = null, $client_id = null)
    {
        $menu_group = new MenuGroup();

        // admin menu
        if (isset($role_hash['ROLE_ADMIN']))
        {
            $menu_group->addItem(new MenuItem(
                'account',
                'menu.account_management',
                '/accounts',
                'icon-signal'
            ));
            $menu_group->addItem(new MenuItem(
                'number',
                'menu.number_management',
                '/numbers',
                'icon-th'
            ));
        }
        // normal user menu
        else 
        {
            if ($client_id != null)
            {
                $menu_group->addItem(new MenuItem(
                    'campaigns',
                    'menu.campaigns',
                    '/client/' . $client_id . '/campaigns',
                    'icon-signal'
                ));
                $menu_group->addItem(new MenuItem(
                    'call_log',
                    'menu.call_log',
                    '/client/' . $client_id . '/call_log',
                    'icon-phone'
                ));
                $menu_group->addItem(new MenuItem(
                    'number',
                    'menu.number_management',
                    '/client/' . $client_id . '/numbers',
                    'icon-th'
                ));
                /*
                $menu_group->addItem(new MenuItem(
                    'quotas',
                    'Usage & Billing',
                    '/client/' . $client_id . '/billing',
                    'icon-tasks'
                ));
                */
            }
        }

        $menu_group->setActive($active);

        return $menu_group;
    }
}
