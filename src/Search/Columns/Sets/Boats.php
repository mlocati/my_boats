<?php

namespace MyBoats\Search\Columns\Sets;

use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\Set;
use MyBoats\Search\Columns\YesNoColumn;

/**
 * Columns set for the MyBoats boats list.
 */
class Boats extends Set
{
    /**
     * Intializes the instance.
     */
    public function __construct()
    {
        $this->addColumn(new Column(
            'b.name',
            t('Name'),
            'getName',
            true
        ));
        $this->addColumn(new YesNoColumn(
            'b.enabled',
            tc('Boat', 'Enabled'),
            'isEnabled',
            true
        ));
        $this->addColumn(new Column(
            'b.length',
            t('Length'),
            'getLength',
            true
        ));

        $defaultSortColumn = $this->getColumnByKey('b.name');
        $this->setDefaultSortColumn($defaultSortColumn, 'asc');
    }
}
