<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Entity;

use FOS\UserBundle\Model\Group as BaseGroup;

/**
 * @deprecated directly extend the classes in the Model namespace
 */
class Group extends BaseGroup
{
    public function __construct($name, $roles = array())
    {
        trigger_error(sprintf('%s is deprecated. Extend FOS\UserBundle\Model\Group directly.', __CLASS__), E_USER_DEPRECATED);
        parent::__construct($name, $roles);
    }
}
