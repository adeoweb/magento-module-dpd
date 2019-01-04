<?php

namespace AdeoWeb\Dpd\Model\Location\Source;

use AdeoWeb\Dpd\Api\Data\LocationInterface;
use Magento\Framework\Data\OptionSourceInterface;

class Type implements OptionSourceInterface
{
    /**
     * @var array
     */
    private $types = [
        LocationInterface::TYPE_WAREHOUSE => 'Warehouse',
        LocationInterface::TYPE_DESTINATION => 'Destination'
    ];

    /**
     * {@inheritDoc}
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->types as $key => $value) {
            $options[] = [
                'label' => __($value),
                'value' => $key,
            ];
        }
        return $options;
    }
}