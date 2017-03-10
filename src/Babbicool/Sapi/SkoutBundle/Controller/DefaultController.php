<?php

namespace Babbicool\Sapi\SkoutBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('BabbicoolSapiSkoutBundle:Default:index.html.twig');
    }
}
