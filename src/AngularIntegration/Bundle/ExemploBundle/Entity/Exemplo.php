<?php

namespace AngularIntegration\Bundle\ExemploBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Exemplo
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AngularIntegration\Bundle\ExemploBundle\Repository\ExemploRepository")
 */
class Exemplo extends \AngularIntegration\Bundle\CoreBundle\Entity\AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    public $name;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Exemplo
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }
}
