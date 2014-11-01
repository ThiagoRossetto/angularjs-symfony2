<?php

namespace AngularIntegration\Bundle\ExemploBundle\Service;

use AngularIntegration\Bundle\ExemploBundle\Entity\Exemplo;
use AngularIntegration\Bundle\CoreBundle\Service\AbstractService;


class ExemploService extends AbstractService
{

    public function insert( Exemplo $entity )
    {
        $insert = $this->getRepository( Exemplo::$NAME )->insert( $entity );
        $this->getManager()->flush();

        return $insert;
    }

    public function update( Exemplo $entity )
    {
        $update = $this->getRepository( Exemplo::$NAME )->update( $entity );
        $this->getManager()->flush();

        return $update;
    }

    public function remove( Exemplo $entity )
    {
        $this->getRepository( Exemplo::$NAME )->remove( $entity->id );
         $this->getManager()->flush();
    }

    public function findById( Exemplo $entity )
    {
        $find = $this->getRepository( Exemplo::$NAME )->findById( $entity->id );
        return $find;
    }

    public function listByFilters( $filters )
    {
        return $this->getRepository( Exemplo::$NAME )->listByFilters( null, null );
    }
}
