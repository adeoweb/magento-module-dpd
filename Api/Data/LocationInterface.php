<?php

namespace AdeoWeb\Dpd\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface LocationInterface extends ExtensibleDataInterface
{
    public const CITY = 'city';
    public const LOCATION_ID = 'location_id';
    public const CONTACT_NAME = 'contact_name';
    public const PHONE = 'phone';
    public const TYPE = 'type';
    public const ADDITIONAL_INFO = 'additional_info';
    public const UPDATED_AT = 'updated_at';
    public const CREATED_AT = 'created_at';
    public const ADDRESS = 'address';
    public const COUNTRY = 'country';
    public const POSTCODE = 'postcode';
    public const WORK_UNTIL = 'work_until';
    public const NAME = 'name';

    public const TYPE_WAREHOUSE = 1;
    public const TYPE_DESTINATION = 2;

    /**
     * Public method
     *
     * @return string
     */
    public function getLocationId();

    /**
     * Public method
     *
     * @param string $locationId
     * @return LocationInterface
     */
    public function setLocationId($locationId);

    /**
     * Public method
     *
     * @return \AdeoWeb\Dpd\Api\Data\LocationExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Public method
     *
     * @param \AdeoWeb\Dpd\Api\Data\LocationExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\AdeoWeb\Dpd\Api\Data\LocationExtensionInterface $extensionAttributes);

    /**
     * Public method
     *
     * @return string
     */
    public function getType();

    /**
     * Public method
     *
     * @param string $type
     * @return LocationInterface
     */
    public function setType($type);

    /**
     * Public method
     *
     * @return string
     */
    public function getName();

    /**
     * Public method
     *
     * @param string $name
     * @return LocationInterface
     */
    public function setName($name);

    /**
     * Public method
     *
     * @return string
     */
    public function getAddress();

    /**
     * Public method
     *
     * @param string $address
     * @return LocationInterface
     */
    public function setAddress($address);

    /**
     * Public method
     *
     * @return string
     */
    public function getCity();

    /**
     * Public method
     *
     * @param string $city
     * @return LocationInterface
     */
    public function setCity($city);

    /**
     * Public method
     *
     * @return string
     */
    public function getCountry();

    /**
     * Public method
     *
     * @param string $country
     * @return LocationInterface
     */
    public function setCountry($country);

    /**
     * Public method
     *
     * @return string
     */
    public function getPostcode();

    /**
     * Public method
     *
     * @param string $postcode
     * @return LocationInterface
     */
    public function setPostcode($postcode);

    /**
     * Public method
     *
     * @return string
     */
    public function getAdditionalInfo();

    /**
     * Public method
     *
     * @param string $additionalInfo
     * @return LocationInterface
     */
    public function setAdditionalInfo($additionalInfo);

    /**
     * Public method
     *
     * @return string
     */
    public function getContactName();

    /**
     * Public method
     *
     * @param string $contactName
     * @return LocationInterface
     */
    public function setContactName($contactName);

    /**
     * Public method
     *
     * @return string
     */
    public function getPhone();

    /**
     * Public method
     *
     * @param string $phone
     * @return LocationInterface
     */
    public function setPhone($phone);

    /**
     * Public method
     *
     * @return string
     */
    public function getWorkUntil();

    /**
     * Public method
     *
     * @param string $workUntil
     * @return LocationInterface
     */
    public function setWorkUntil($workUntil);

    /**
     * Public method
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Public method
     *
     * @param string $createdAt
     * @return LocationInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Public method
     *
     * @return string
     */
    public function getUpdatedAt();

    /**
     * Public method
     *
     * @param string $updatedAt
     * @return LocationInterface
     */
    public function setUpdatedAt($updatedAt);
}
