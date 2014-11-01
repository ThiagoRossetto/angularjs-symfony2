<?php

namespace AngularIntegration\Bundle\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Request;

/**
 * Class ResourcesController
 * @package Prognus\API\Controller
 */
class ResourcesController extends Controller
{
    /**
     * @param $module - Nome do módulo para fazer a busca pelos assets
     * @return Response
     */
    public function getResourceAction(Request $request, $module)
    {
        //-- Verifica se a query string está vazia
        if( !$request->get("file") )
            return new Response('File not Found!' , 400 );

        //-- Coloca a primeira letra do nome do módulo como maiúscula
        $module = ucfirst($module);

        //-- Pega o caminho do arquivo fornecido através de query string
        $rFile = str_replace('../' , '', $request->get("file"));
        $file = __DIR__ . "/../../{$module}Bundle/Resources/public/".$rFile;

        //-- Verifica se o arquivo existe
        if( file_exists( $file ) ){
            //-- Recupera as informações do arquivo
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $minetype = finfo_file($finfo, $file);
            finfo_close($finfo);

            //-- Recupera o serviço do fileduck
            $fileDuck =  $this->get( 'FileDuck' );

            //-- Recupera as configurações do fileduck
            $config = $fileDuck->getConfig();

            //-- Seta as configurações do filecuck
            $fileDuck->setConfig($config);

            //-- Adiciona o arquivo para o fileduck recuperar as informações dele
            $fileDuck->add( $file );

            //-- Coloca na variavel response o conteúdo do arquivo
            $response = $fileDuck->renderFile($minetype);
        }
        else
            $response = new Response('File not Found!' , 404 );

        //-- Responde o conteúdo do arquivo, caso ele seja encontrado
        return $response;
    }

    public function getUserMeAction(){
        $oUser = $this->getUser();

        $user = array();
        $user['me']['data']['username'] = $oUser->getUsername();
        $user['me']['data']['displayName'] = $oUser->getDisplayname();
        $user['me']['data']['givenname'] = $oUser->getGivenname();
        $user['me']['data']['surname'] = $oUser->getSurname();
        $user['me']['data']['roles'] = $oUser->getRoles();
        $user['me']['data']['email'] = $oUser->getEmail();
        $user['me']['data']['salt'] = $oUser->getSalt();
        $user['me']['data']['dn'] = $oUser->getDn();
        $user['me']['data']['cn'] = $oUser->getCn();

        $user['me']['browser'] = $this->getBrowser();

        return new Response(json_encode($user));
    }

    public function getBrowser()
    {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';

        // Next get the name of the useragent yes seperately and for good reason
        if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
        {
            $bname = 'ie';
        }
        elseif(preg_match('/rv:11.0/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
        {
            $bname = 'ie';
        }

        // check if we have a number
        return array(
            'userAgent' => $u_agent,
            'name'      => $bname,
        );
    }

    public function downloadFileAction($file){
        $path = '/tmp/'.$file;
        $handle = fopen($path, 'r');
        header('Content-Type: application/csv');
        header('Content-Disposition:attachment;filename='.$file.'');
        readfile($path);
        fclose($handle);
        unlink($path);
        //exit();
        return new Response();
    }
}