<?php

namespace AdeoWeb\Dpd\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface LocationInterface extends ExtensibleDataInterface
{
    const CITY = 'city';
    const LOCATION_ID = 'location_id';
    const CONTACT_NAME = 'contact_name';
    const PHONE = 'phone';
    const TYPE = 'type';
    const ADDITIONAL_INFO = 'additional_info';
    const UPDATED_AT = 'updated_at';
    const CREATED_AT = 'created_at';
    const ADDRESS = 'address';
    const COUNTRY = 'country';
    const POSTCODE = 'postcode';
    const WORK_UNTIL = 'work_until';
    const NAME = 'name';

    const TYPE_WAREHOUSE = 1;
    const TYPE_DESTINATION = 2;

    /**
     * @return string
     */
    public function getLocationId();

    /**
     * @param string $locationId
     * @return LocationInterface
     */
    public function setLocationId($locationId);

    /**
     * @return \AdeoWeb\Dpd\Api\Data\LocationExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * @param \AdeoWeb\Dpd\Api\Data\LocationExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\AdeoWeb\Dpd\Api\Data\LocationExtensionInterface $extensionAttributes);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     * @return LocationInterface
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return LocationInterface
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getAddress();

    /**
     * @param string $address
     * @return LocationInterface
     */
    public function setAddress($address);

    /**
     * @return string
     */
    public function getCity();

    /**
     * @param string $city
     * @return LocationInterface
     */
    public function setCity($city);

    /**
     * @return string
     */
    public function getCountry();

    /**
     * @param string $country
     * @return LocationInterface
     */
    public function setCountry($country);

    /**
     * @return string
     */
    public function getPostcode();

    /**
     * @param string $postcode
     * @return LocationInterface
     */
    public function setPostcode($postcode);

    /**
     * @return string
     */
    public function getAdditionalInfo();

    /**
     * @param string $additionalInfo
     * @return LocationInterface
     */
    public function setAdditionalInfo($additionalInfo);

    /**
     * @return string
     */
    public function getContactName();

    /**
     * @param string $contactName
     * @return LocationInterface
     */
    public function setContactName($contactName);

    /**
     * @return string
     */
    public function getPhone();

    /**
     * @param string $phone
     * @return LocationInterface
     */
    public function setPhone($phone);

    /**
     * @return string
     */
    public function getWorkUntil();

    /**
     * @param string $workUntil
     * @return LocationInterface
     */
    public function setWorkUntil($workUntil);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $createdAt
     * @return LocationInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param string $updatedAt
     * @return LocationInterface
     */
    public function setUpdatedAt($updatedAt);
}
