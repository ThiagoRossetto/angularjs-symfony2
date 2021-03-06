<?php
/**
 * Created by PhpStorm.
 * User: thiago
 * Date: 30/05/14
 * Time: 14:02
 */

namespace AngularIntegration\Bundle\CoreBundle\Service;


class UploadImage {

    /**
     * @param $images
     * @param $attributes
     * @return bool
     */
    public function verify($images, $attributes) {

        foreach( $images as $image ) {
            if ( $image->size > $attributes['size'] ) return false; //* informar qual o tamanho  permitido, e caso der erro, informar o erro *//
            if ( $image->type != $attributes['type']) return false; //* informar quais os tipos permitidos, e caso der erro, informar o erro *//
        }
    }

    /**
     * @param $path: Caminho pelo web do bundle que possui as imagens
     * @param $folder: qual é o nome do folder
     * @param $images: array de imagem
     */
    public function insert($path, $images) {

        if( file_exists($path) )
            $this->deleteDir($path);

        if( !file_exists($path) )
            mkdir($path, 0777, true);

        foreach( $images as $image ){

            $matches = array();
            preg_match_all("/data:([^,]*);base64,([a-zA-Z0-9\+\/\=]+)/i", $image->img, $matches,  PREG_SET_ORDER);

            $type = $image->type ? explode("/", $image->type) : "";
            $type = $type ? $type[1] : "jpg";

            $fileName = str_replace("/", "", md5($image->name . time()) );

            file_put_contents($path."/".$fileName.".".$type, base64_decode($matches[0][2]));
        }
    }

    public function removeAll( $path ) {
        if( file_exists($path) )
            return $this->deleteDir($path);
        else
            return array("error" => "A pasta informada não existe.");
    }

    public function listAll( $path ){

        if( !file_exists($path) )
            return array();

        $dir = opendir($path);

        $images = array();

        while (($image = readdir($dir)) !== false) {
            if( in_array( $image, array('.','..') ) )
                continue;

            $type = pathinfo($path. "/". $image, PATHINFO_EXTENSION);
            $data = file_get_contents($path. "/". $image);
            $size = filesize($path. "/". $image);
            $images[] = array('name'=> $image,'img' => 'data:image/' . $type . ';base64,' . base64_encode($data), 'size' => $size, 'type' => 'image/'.$type );
        }

        return $images;
    }

    public function deleteDir($dir)
    {
        if (substr($dir, strlen($dir)-1, 1) != '/')
            $dir .= '/';

        if ($handle = opendir($dir))
        {
            while ($obj = readdir($handle))
            {
                if ($obj != '.' && $obj != '..')
                {
                    if (is_dir($dir.$obj))
                    {
                        if (!deleteDir($dir.$obj))
                            return false;
                    }
                    elseif (is_file($dir.$obj))
                    {
                        if (!unlink($dir.$obj))
                            return false;
                    }
                }
            }

            closedir($handle);

            if (!@rmdir($dir))
                return false;
            return true;
        }
        return false;
    }
} 