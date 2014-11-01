<?php

namespace AngularIntegration\Bundle\ExemploBundle\Repository;

use AngularIntegration\Bundle\CoreBundle\Repository\AbstractRepository;
use AngularIntegration\Bundle\CoreBundle\Entity\Paging\PageRequest;
use AngularIntegration\Bundle\CoreBundle\Entity\AbstractEntity;
use AngularIntegration\Bundle\CoreBundle\Entity\Paging\Page;

class ExemploRepository extends AbstractRepository
{
    /**
     * Refatorar esta classe
     */
    /**
     * @param array $filters
     * @param PageRequest $pageRequest
     * @return \AngularIntegration\Bundle\CoreBundle\Entity\Paging\Page | void
     */
    public function listByFilters( AbstractEntity $filters = null, PageRequest $pageRequest = null )
    {
        $statement = $this->getEntityManager()->createQueryBuilder();

        $query = $statement
            ->select( 'e' )
            ->from('AngularIntegration\Bundle\ExemploBundle\Entity\Exemplo', 'e');

        if ( $filters == null )
        {

        }

        if ( $pageRequest == null )
        {

        } else{
            //$pageRequest = (object) array("page" => (object) array("content" => $query->getQuery()->getArrayResult()));
        }
        $pageRequest = (object) array("page" => (object) array("content" => $query->getQuery()->getArrayResult()));

        return $pageRequest;
    }

}