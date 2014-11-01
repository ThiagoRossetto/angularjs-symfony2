<?php
/**
 * Created by PhpStorm.
 * User: thiago
 * Date: 15/05/14
 * Time: 09:39
 */

namespace AngularIntegration\Bundle\CoreBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 *
 * @ORM\MappedSuperclass
 */
abstract class AbstractEntity
{
    /**
     * The class name to used when querying
     *
     * @var string
     */
    public static $NAME;
    /**
     * @var string
     */
    public $_explicitType;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    public function __construct()
    {
        self::$NAME = get_called_class();
        $this->_explicitType = get_called_class();
    }
}
