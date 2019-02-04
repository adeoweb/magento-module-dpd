<?php

namespace AdeoWeb\Dpd\Model\Data;

use AdeoWeb\Dpd\Api\Data\LocationExtensionInterface;
use AdeoWeb\Dpd\Api\Data\LocationInterface;

/**
 * Class Location
 * @codeCoverageIgnore
 */
class Location extends \Magento\Framework\Api\AbstractExtensibleObject implements LocationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getLocationId()
    {
        return $this->_get(self::LOCATION_ID);
    }

    /**
     * {@inheritDoc}
     */
    public function setLocationId($locationId)
    {
        return $this->setData(self::LOCATION_ID, $locationId);
    }

    /**
     * {@inheritDoc}
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritDoc}
     */
    public function setExtensionAttributes(LocationExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return $this->_get(self::TYPE);
    }

    /**
     * {@inheritDoc}
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * {@inheritDoc}
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * {@inheritDoc}
     */
    public function getAddress()
    {
        return $this->_get(self::ADDRESS);
    }

    /**
     * {@inheritDoc}
     */
    public function setAddress($address)
    {
        return $this->setData(self::ADDRESS, $address);
    }

    /**
     * {@inheritDoc}
     */
    public function getCity()
    {
        return $this->_get(self::CITY);
    }

    /**
     * {@inheritDoc}
     */
    public function setCity($city)
    {
        return $this->setData(self::CITY, $city);
    }

    /**
     * {@inheritDoc}
     */
    public function getCountry()
    {
        return $this->_get(self::COUNTRY);
    }

    /**
     * {@inheritDoc}
     */
    public function setCountry($country)
    {
        return $this->setData(self::COUNTRY, $country);
    }

    /**
     * {@inheritDoc}
     */
    public function getPostcode()
    {
        return $this->_get(self::POSTCODE);
    }

    /**
     * {@inheritDoc}
     */
    public function setPostcode($postcode)
    {
        return $this->setData(self::POSTCODE, $postcode);
    }

    /**
     * {@inheritDoc}
     */
    public function getAdditionalInfo()
    {
        return $this->_get(self::ADDITIONAL_INFO);
    }

    /**
     * {@inheritDoc}
     */
    public function setAdditionalInfo($additionalInfo)
    {
        return $this->setData(self::ADDITIONAL_INFO, $additionalInfo);
    }

    /**
     * {@inheritDoc}
     */
    public function getContactName()
    {
        return $this->_get(self::CONTACT_NAME);
    }

    /**
     * {@inheritDoc}
     */
    public function setContactName($contactName)
    {
        return $this->setData(self::CONTACT_NAME, $contactName);
    }

    /**
     * {@inheritDoc}
     */
    public function getPhone()
    {
        return $this->_get(self::PHONE);
    }

    /**
     * {@inheritDoc}
     */
    public function setPhone($phone)
    {
        return $this->setData(self::PHONE, $phone);
    }

    /**
     * {@inheritDoc}
     */
    public function getWorkUntil()
    {
        return $this->_get(self::WORK_UNTIL);
    }

    /**
     * {@inheritDoc}
     */
    public function setWorkUntil($workUntil)
    {
        return $this->setData(self::WORK_UNTIL, $workUntil);
    }

    /**
     * {@inheritDoc}
     */
    public function getCreatedAt()
    {
        return $this->_get(self::CREATED_AT);
    }

    /**
     * {@inheritDoc}
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * {@inheritDoc}
     */
    public function getUpdatedAt()
    {
        return $this->_get(self::UPDATED_AT);
    }

    /**
     * {@inheritDoc}
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * {@inheritDoc}
     */
    public function setData($key, $value = null)
    {
        if ($key === (array)$key) {
            $this->_data = $key;
        } else {
            $this->_data[$key] = $value;
        }

        return $this;
    }
}
