<?php

namespace AngularIntegration\Bundle\CoreBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Atividade
 *
 * @ORM\Table(name="entidade_teste")
 * @ORM\Entity(repositoryClass="AngularIntegration\Bundle\CoreBundle\Repository\EntidadeTesteRepository")
 */
class EntidadeTeste extends AbstractEntity
{

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    public $descricao;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", length=255)
     */
    public $ativo;

    /**
     * @var decimal
     *
     * @ORM\Column(type="decimal", length=255)
     */
    public $valor;

    /**
     * @var datetime
     *
     * @ORM\Column(type="datetime", length=255)
     */
    public $criacao;
}
