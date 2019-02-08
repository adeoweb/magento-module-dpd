<?php

namespace AdeoWeb\Dpd\Model;

use AdeoWeb\Dpd\Api\Data\LocationInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use AdeoWeb\Dpd\Api\Data\LocationInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

class Location extends AbstractModel
{
    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var LocationInterfaceFactory
     */
    protected $locationDataFactory;

    /**
     * @var string
     */
    protected $_eventPrefix = 'adeoweb_dpd_location';

    /**
     * @var string
     */
    protected $_idFieldName = 'location_id';

    public function __construct(
        Context $context,
        Registry $registry,
        LocationInterfaceFactory $locationDataFactory,
        DataObjectHelper $dataObjectHelper,
        ResourceModel\Location $resource,
        ResourceModel\Location\Collection $resourceCollection,
        array $data = []
    ) {
        $this->locationDataFactory = $locationDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @return LocationInterface
     */
    public function getDataModel()
    {
        $locationData = $this->getData();
        
        $locationDataObject = $this->locationDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $locationDataObject,
            $locationData,
            LocationInterface::class
        );
        
        return $locationDataObject;
    }
}
