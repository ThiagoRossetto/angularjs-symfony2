<?php

namespace Operadores\Bundle\CoreBundle\Controller;

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
     * @param $module
     * @return Response
     */
    public function getResourceAction(Request $request, $module)
    {
        if( !$request->get("file") )
            return new Response('File not Found!' , 400 );

        $langs = $request->getLanguages();

        if($langs[0] == "es" || $langs[0] == "es_419")
            $request->setLocale("es_ES");
        else
            $request->setLocale("pt_BR");

        $module = ucfirst($module);

        $rFile = str_replace('../' , '', $request->get("file"));
        $file = __DIR__ . "/../../{$module}Bundle/Resources/public/".$rFile;

        if( file_exists( $file ) ){
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $minetype = finfo_file($finfo, $file);
            finfo_close($finfo);

            $fileDuck =  $this->get( 'FileDuck' );

            $config = $fileDuck->getConfig();

            $config['lang'] = $request->getLocale();

            $fileDuck->setConfig($config);
            $fileDuck->add( $file );
            $response = $fileDuck->renderFile($minetype);
        }
        else
            $response = new Response('File not Found!' , 404 );

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