<?php

namespace AngularIntegration\Bundle\CoreBundle\Controller;

use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FrontController extends Controller
{
    /**
     * /broker URL
     * Request $request - Injeção de dependência da solicitação
     * @return Response
     */
    public function indexAction(Request $request)
    {

        $config = new \Amfphp_Core_Config();

        //--Registra quais são as entidades da aplicação
        $voFolders = array();
        $dir = dirname(__FILE__) . '/../../';
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if(strpos($file, ".") === false)
                    $voFolders[] = array(dirname(__FILE__) . '/../../'.$file.'/Entity/', 
                                            'AngularIntegration\\Bundle\\'.$file.'\\Entity');
            }
            closedir($dh);
        }
        $config->pluginsConfig["AmfphpVoConverter"] = array('voFolders' => $voFolders, 'enforceConversion' => true);

        //-- Registra o local onde está o responsável para ocontrole de erro
        $voFolders[] = array(dirname(__FILE__) . '/../Entity/Error/', 
                                'AngularIntegration\\Bundle\\CoreBundle\\Entity\\Error');
        //--Registra o local onde está o responsável para a páginação de dados
        $voFolders[] = array(dirname(__FILE__) . '/../Entity/Paging/', 
                                'AngularIntegration\\Bundle\\CoreBundle\\Entity\\Paging');


        $config->pluginsConfig["AmfphpVoConverter"] = array('voFolders' => $voFolders);

        //-- Registra quais são os serviços da aplicação
        foreach(glob(dirname(__FILE__) . '/../../*', GLOB_ONLYDIR) as $dir) {
            $bundle = explode("/", $dir);
            $bundle = $bundle[count($bundle)-1];
            $config->serviceFolders[] = array($dir.'/Service/', 
                                                'AngularIntegration\\Bundle\\'.$bundle.'\\Service');
        }

        //-- Registra os serviços e entidades especificadas acima
        $gateway = \Amfphp_Core_HttpRequestGatewayFactory::createGateway($config);
        //-- Executa o Serviço solicitado do front-end
        $gateway->service();

        //-- Retornando um response válido para o front-end
        return new Response( $gateway->output() );
    }
}
