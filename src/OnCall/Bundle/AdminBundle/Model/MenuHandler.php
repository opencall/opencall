<?php

namespace OnCall\Bundle\AdminBundle\Model;

class MenuHandler
{
    static public function getMenu($role_hash, $active = null, $client_id = null, $params = array())
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
                $call_log_params = array();
                $camp_params = array();

                if (isset($params['dts']))
                {
                    $call_log_params['dts'] = $params['dts'];
                    $camp_params['date_from'] = $params['dts'];
                }
                if (isset($params['dte']))
                {
                    $call_log_params['dte'] = $params['dte'];
                    $camp_params['date_to'] = $params['dte'];
                }
                if (isset($params['date_from']))
                {
                    $call_log_params['dts'] = $params['date_from'];
                    $camp_params['date_from'] = $params['date_from'];
                }
                if (isset($params['date_to']))
                {
                    $call_log_params['dte'] = $params['date_to'];
                    $camp_params['date_to'] = $params['date_to'];
                }

                $menu_group->addItem(new MenuItem(
                    'campaigns',
                    'menu.campaigns',
                    '/client/' . $client_id . '/campaigns?' . http_build_query($camp_params),
                    'icon-signal'
                ));
                $menu_group->addItem(new MenuItem(
                    'call_log',
                    'menu.call_log',
                    '/client/' . $client_id . '/call_log?' . http_build_query($call_log_params),
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
