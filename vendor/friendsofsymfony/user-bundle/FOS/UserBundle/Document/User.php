<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Document;

use FOS\UserBundle\Model\User as AbstractUser;

/**
 * @deprecated directly extend the classes in the Model namespace
 */
abstract class User extends AbstractUser
{
    public function __construct()
    {
        trigger_error(sprintf('%s is deprecated. Extend FOS\UserBundle\Model\User directly.', __CLASS__), E_USER_DEPRECATED);
        parent::__construct();
    }
}
