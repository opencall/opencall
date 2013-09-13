<?php

namespace OnCall\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use OnCall\Bundle\AdminBundle\Model\MenuHandler;

class MainController extends Controller
{
    public function indexAction()
    {
        return $this->render(
            'OnCallAdminBundle:Main:index.html.twig',
            array(
                'sidebar_menu' => MenuHandler::getMenu()
            )
        );

    }
}
