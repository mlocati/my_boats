<?php

namespace MyBoats\Search\Results\Item;

use Concrete\Core\Search\Column\Set;
use Concrete\Core\Search\Result\Item;
use Concrete\Core\Search\Result\Result;
use URL;

class Boats extends Item
{
    /**
     * @var \MyBoats\Entity\Boat
     */
    protected $entity;

    public function __construct(Result $result, Set $columns, $item)
    {
        parent::__construct($result, $columns, $item);
        $this->entity = $item;
    }

    /**
     * Get the CSS class for the list view.
     *
     * @return string
     */
    public function getRowClass()
    {
        return $this->entity->isEnabled() ? 'boat-enabled' : 'boat-disabled';
    }

    /**
     * Get the URL of the details page.
     *
     * @return string
     */
    public function getViewUrl()
    {
        return URL::to('/dashboard/boats/details', $this->entity->getId());
    }
}
