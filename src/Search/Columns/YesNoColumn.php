<?php

namespace MyBoats\Search\Columns;

use Concrete\Core\Search\Column\Column;

/**
 * Column type that renders yes/no values.
 */
class YesNoColumn extends Column
{
    /**
     * {@inheritdoc}
     *
     * @see Column::getColumnValue()
     */
    public function getColumnValue($obj)
    {
        $value = parent::getColumnValue($obj);

        if ($value === null) {
            $result = '';
        } else {
            $result = $value ? t('Yes') : t('No');
        }

        return $result;
    }
}
