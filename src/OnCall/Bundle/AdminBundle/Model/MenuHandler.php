<?php

namespace OnCall\Bundle\AdminBundle\Model;

class MenuHandler
{
    static public function getMenu($active = null)
    {
        $menu_group = new MenuGroup();
        $menu_group->addItem(new MenuItem(
            'account',
            'Account Management',
            '/accounts',
            'icon-signal'
        ));
        $menu_group->addItem(new MenuItem(
            'number',
            'Number Management',
            '/numbers',
            'icon-th'
        ));

        $menu_group->setActive($active);

        return $menu_group;
    }
}
