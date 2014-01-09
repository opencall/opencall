<?php

namespace OnCall\Bundle\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use OnCall\Bundle\AdminBundle\Model\Controller;

class LocaleController extends Controller
{
    public function changeAction($locale)
    {
        $request = $this->getRequest();
        // error_log('setting locale to - ' . $locale);
        $request->getSession()->set('_locale', $locale);

        return $this->redirect($request->headers->get('referer'));
    }
}
