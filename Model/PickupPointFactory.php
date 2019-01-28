<?php

namespace AdeoWeb\Dpd\Model;

use AdeoWeb\Dpd\Api\Data\PickupPointInterface;
use AdeoWeb\Dpd\Model\PickupPoint\ResolverInterface;
use Magento\Framework\DataObject;
use Magento\Framework\ObjectManagerInterface;

class PickupPointFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var string
     */
    private $instanceName;

    /**
     * @var PickupPoint\ResolverInterface
     */
    private $typeResolver;

    public function __construct(
        ObjectManagerInterface $objectManager,
        ResolverInterface $typeResolver,
        $instanceName = '\\AdeoWeb\\Dpd\\Api\\Data\\PickupPointInterface'
    ) {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
        $this->typeResolver = $typeResolver;
    }

    /**
     * @param array $data
     * @return \AdeoWeb\Dpd\Api\Data\PickupPointInterface
     * @codeCoverageIgnore
     */
    public function create(array $data = [])
    {
        return $this->objectManager->create($this->instanceName, $data);
    }

    /**
     * @param array|DataObject $responseData
     * @param array $objectData
     * @return \AdeoWeb\Dpd\Api\Data\PickupPointInterface
     */
    public function createFromResponseData($responseData, array $objectData = [])
    {
        if (\is_array($responseData)) {
            $responseData = new DataObject($responseData);
        }

        /** @var PickupPointInterface $object */
        $object = $this->objectManager->create($this->instanceName, $objectData);
        $object->setApiId($responseData->getData('parcelshop_id'));
        $object->setCompany($responseData->getData('company'));
        $object->setCountry($responseData->getData('country'));
        $object->setCity($responseData->getData('city'));
        $object->setPostcode($responseData->getData('pcode'));
        $object->setStreet($responseData->getData('street'));
        $object->setEmail($responseData->getData('email'));
        $object->setPhone($responseData->getData('phone'));
        $object->setLongitude($responseData->getData('longitude'));
        $object->setLatitude($responseData->getData('latitude'));

        $type = $this->typeResolver->resolve($object);
        if ($type) {
            $object->setType($type);
        }

        return $object;
    }
}