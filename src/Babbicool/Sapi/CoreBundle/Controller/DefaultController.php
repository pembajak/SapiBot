<?php

namespace Babbicool\Sapi\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('BabbicoolSapiCoreBundle:Default:index.html.twig');
    }
}
