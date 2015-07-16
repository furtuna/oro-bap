<?php

namespace BAP\SimpleBTSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('BAPSimpleBTSBundle:Default:index.html.twig', array('name' => $name));
    }
}
