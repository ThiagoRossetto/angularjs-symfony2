<?php

namespace AngularIntegration\Bundle\CoreBundle\Entity\Error;
/**
 * Created by PhpStorm.
 * User: Gustavo
 * Date: 23/06/14
 * Time: 11:13
 */

class Error{
    /*
     *
     */
    public $error;

    /**
     * Initializes a new Error.
     *
     * @param erro $erro
     */
    public function __construct($error)
    {
        $this->error = mb_convert_encoding($error, 'utf-8' ,mb_detect_encoding($error));
    }
}