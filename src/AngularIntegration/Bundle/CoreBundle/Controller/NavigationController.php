<?php

namespace AngularIntegration\Bundle\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AngularIntegration\Bundle\CoreBundle\Service\API;

class NavigationController extends Controller
{
    public function indexAction(Request $request, $module)
    {
        /*if( $module == 'login' )
        {
            header("Location: system/login");
            exit;
        }

        /**
          * Caso a rota não existir, chame o checklist!
         *
        if( !in_array($module, array('admin')) )
        {
            header("Location: ".$_SERVER['REDIRECT_URL']."/../");
            exit;
        }*/
        return $this->get('render_module')->render('AngularIntegration'.ucfirst($module).'Bundle:Default:', array(), $this->getUser());
    }
}
