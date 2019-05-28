<?php

namespace AdeoWeb\Dpd\Model;

use AdeoWeb\Dpd\Api\Data\PickupPointInterface;
use Magento\Framework\Model\AbstractModel;
use AdeoWeb\Dpd\Model\ResourceModel\PickupPoint as PickupPointResource;

/**
 * Class PickupPoint
 * @codeCoverageIgnore
 */
class PickupPoint extends AbstractModel implements PickupPointInterface
{

    /**
     * {@inheritDoc}
     */
    protected function _construct()
    {
        $this->_init(PickupPointResource::class);
    }

    /**
     * @return int
     */
    public function getPickupPointId()
    {
        return $this->getData('pickup_point_id');
    }

    /**
     * @param int $pickupPointId
     * @return PickupPoint
     */
    public function setPickupPointId($pickupPointId)
    {
        return $this->setData('pickup_point_id', $pickupPointId);
    }

    /**
     * @return string
     */
    public function getApiId()
    {
        return $this->getData('api_id');
    }

    /**
     * @param string $apiId
     * @return PickupPoint
     */
    public function setApiId($apiId)
    {
        return $this->setData('api_id', $apiId);
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->getData('type');
    }

    /**
     * @param int $type
     * @return PickupPoint
     */
    public function setType($type)
    {
        return $this->setData('type', $type);

    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->getData('company');
    }

    /**
     * @param string $company
     * @return PickupPoint
     */
    public function setCompany($company)
    {
        return $this->setData('company', $company);

    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->getData('country');
    }

    /**
     * @param string $country
     * @return PickupPoint
     */
    public function setCountry($country)
    {
        return $this->setData('country', $country);
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->getData('city');
    }

    /**
     * @param string $city
     * @return PickupPoint
     */
    public function setCity($city)
    {
        return $this->setData('city', $city);
    }

    /**
     * @return string
     */
    public function getPostcode()
    {
        return $this->getData('postcode');
    }

    /**
     * @param string $postcode
     * @return PickupPoint
     */
    public function setPostcode($postcode)
    {
        return $this->setData('postcode', $postcode);
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->getData('street');
    }

    /**
     * @param string $street
     * @return PickupPoint
     */
    public function setStreet($street)
    {
        return $this->setData('street', $street);
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->getData('email');
    }

    /**
     * @param string $email
     * @return PickupPoint
     */
    public function setEmail($email)
    {
        return $this->setData('email', $email);
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->getData('phone');
    }

    /**
     * @param string $phone
     * @return PickupPoint
     */
    public function setPhone($phone)
    {
        return $this->setData('phone', $phone);
    }

    /**
     * @return string
     */
    public function getLongitude()
    {
        return $this->getData('longitude');
    }

    /**
     * @param string $longitude
     * @return PickupPoint
     */
    public function setLongitude($longitude)
    {
        return $this->setData('longitude', $longitude);
    }

    /**
     * @return string
     */
    public function getLatitude()
    {
        return $this->getData('latitude');
    }

    /**
     * @param string $latitude
     * @return PickupPoint
     */
    public function setLatitude($latitude)
    {
        return $this->setData('latitude', $latitude);
    }

    /**
     * @return array
     */
    public function getOpeningHours()
    {
        return $this->getData('opening_hours');
    }

    /**
     * @param array $openingHours
     * @return PickupPoint
     */
    public function setOpeningHours($openingHours)
    {
        return $this->setData('opening_hours', $openingHours);
    }
}