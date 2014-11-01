<?php
namespace AngularIntegration\Bundle\CoreBundle\Repository;

use AngularIntegration\Bundle\CoreBundle\Entity\AbstractEntity;
use AngularIntegration\Bundle\CoreBundle\Entity\Paging\PageRequest;
use AngularIntegration\Bundle\CoreBundle\Entity\Paging\Page;

/**
 * Interface IRepository
 * @package AngularIntegration\Bundle\CoreBundle\Repository
 */
interface IRepository
{
    /**
     * @param AbstractEntity $entity
     * @return AbstractEntity
     */
    public function insert( AbstractEntity $entity );
    /**
     * @param AbstractEntity $entity
     * @return AbstractEntity
     */
    public function update( AbstractEntity $entity );

    /**
     * @param $entityName
     * @param $id
     */
    public function remove( $id );

    /**
     * @param $id
     * @return AbstractEntity
     */
    public function findById( $id );

    /**
     * @param $filters
     * @param PageRequest $pageRequest
     * @return Page
     */
    public function listByFilters( AbstractEntity $filters = null, PageRequest $pageRequest = null );
}