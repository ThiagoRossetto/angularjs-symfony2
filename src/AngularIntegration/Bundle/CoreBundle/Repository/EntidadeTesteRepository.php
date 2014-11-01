<?php

namespace AngularIntegration\Bundle\CoreBundle\Repository;

use AngularIntegration\Bundle\CoreBundle\Entity\Paging\PageRequest;
use AngularIntegration\Bundle\CoreBundle\Entity\Paging\Page;

class EntidadeTesteRepository extends AbstractRepository
{
    /**
     * @param array $filters
     * @param PageRequest $pageRequest
     * @return \AngularIntegration\Bundle\CoreBundle\Entity\Paging\Page | void
     */
    public function listByFilters( array $filters = null, PageRequest $pageRequest = null )
    {

        if ( $pageRequest == null )
        {

        }

        if ( $filters == null )
        {

        }

        return null;

        $dql = "SELECT new EntidadeTeste(id, asd)
                  FROM EntitidadeTeste AS entidadeTeste
                 WHERE entidadeTeste.descricao LIKE % ? %";
        $query = $this->getEntityManager()->createQuery( "" );

    }
}