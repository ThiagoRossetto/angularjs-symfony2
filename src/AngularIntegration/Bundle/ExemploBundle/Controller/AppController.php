<?php

namespace AngularIntegration\Bundle\ExemploBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
	Symfony\Component\HttpFoundation\JsonResponse;

class AppController extends Controller
{
    public function indexAction()
    {
        return $this->render('AngularIntegrationExemploBundle:App:index.html.twig');
    }
}
