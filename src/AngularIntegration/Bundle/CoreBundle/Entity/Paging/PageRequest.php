<?php
namespace AngularIntegration\Bundle\CoreBundle\Entity\Paging;

/**
 * Class PageRequest
 * @package AngularIntegration\Bundle\CoreBundle\Entity\Paging
 */
class PageRequest
{
    public $page;

    public $sort;

    public function pagination( $query ) {

        $this->page->total = count($query->getQuery()->getArrayResult());
        $this->page->totalPages = ceil($this->page->total / $this->page->range);
        $this->page->page = $this->page->page == '' ? 1 : $this->page->page;

    }

    /**
     * @param mixed $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

    /**
     * @return mixed
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param mixed $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    /**
     * @return mixed
     */
    public function getSort()
    {
        return $this->sort;
    }


}