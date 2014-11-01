<?php
/**
 * Created by PhpStorm.
 * User: thiago
 * Date: 15/05/14
 * Time: 09:51
 */

namespace AngularIntegration\Bundle\CoreBundle\Repository;

use AngularIntegration\Bundle\CoreBundle\Entity\AbstractEntity;
use Doctrine\ORM\EntityRepository;
use AngularIntegration\Bundle\CoreBundle\Entity\Paging\PageRequest;
use AngularIntegration\Bundle\CoreBundle\Entity\Paging\Page;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AbstractRepository
 * @package AngularIntegration\Bundle\CoreBundle\Repository
 */
abstract class AbstractRepository extends EntityRepository implements IRepository
{
    public function getContainer()
    {
        global $kernel;
        return $kernel->getContainer();
    }

    /**
     * @param AbstractEntity $entity
     * @return AbstractEntity
     */
    public function insert( AbstractEntity $entity )
    {
        $this->getEntityManager()->persist( $entity );
        return $entity;
    }

    /**
     * @param AbstractEntity $entity
     * @return AbstractEntity
     */
    public function update( AbstractEntity $entity )
    {
        $entity = $this->getEntityManager()->merge( $entity );
        return $entity;
    }

    /**
     * @param $id
     */
    public function remove( $id )
    {
        //FIXME Verificar se retorna null ou estoura expcetion
        $entity= $this->getEntityManager()->find( $this->getClassName(), $id );

        if ( $entity == null )
        {
            throw new \Exception( 'O identificador '.$id.' n�o foi encontrado para a entidade '.$this->getClassName(), 404 );
        }

        $this->getEntityManager()->remove( $entity );
        return $entity;
    }

    /**
     * @param $id
     * @return AbstractEntity
     */
    public function findById( $id )
    {
        //FIXME Verificar se retorna null ou estoura expcetion
        $entity = $this->getEntityManager()->find( $this->getClassName(), $id );

        if ( $entity == null )
        {
            throw new \Exception( 'O identificador '.$id.' não foi encontrado para a entidade '.$this->getClassName(), 404 );
        }

        return $entity;
    }

    /**
     * @param $id
     * @return AbstractEntity
     */
    public function listAll()
    {
        //FIXME Verificar se retorna null ou estoura expcetion

        $statement = $this->getEntityManager()->createQueryBuilder();

        $query = $statement
            ->select( 'obj' )
            ->from($this->getClassName(), 'obj');

        return $query->getQuery()->getArrayResult();
    }


    /**
     * @param array $filters
     * @param PageRequest $pageRequest
     * @return Page
     */
    public abstract function listByFilters( AbstractEntity $filters = null, PageRequest $pageRequest = null );
}