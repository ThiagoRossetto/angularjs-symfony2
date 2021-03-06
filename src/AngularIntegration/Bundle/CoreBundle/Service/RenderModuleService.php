<?php

namespace AngularIntegration\Bundle\CoreBundle\Service;
use Symfony\Component\DependencyInjection\Container;

class RenderModuleService
{
    public $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    public function render( $content, $parameters = array(),  $user = null, Response $response = null ) {
        return $this->container->get('templating')->renderResponse('AngularIntegrationCoreBundle:Default:index.html.twig', array('content'=> $content, 'parameters'=> $parameters, 'user' => $user), $response);
    }
}