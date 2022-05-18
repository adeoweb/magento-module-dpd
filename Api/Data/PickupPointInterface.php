<?php

namespace AdeoWeb\Dpd\Api\Data;

interface PickupPointInterface
{
    public const PICKUP_POINT_ID = 'pickup_point_id';
    public const API_ID = 'api_id';
    public const TYPE = 'type';
    public const COMPANY = 'company';
    public const COUNTRY = 'country';
    public const CITY = 'city';
    public const POSTCODE = 'postcode';
    public const STREET = 'street';
    public const EMAIL = 'email';
    public const PHONE = 'phone';
    public const LONGITUDE = 'longitude';
    public const LATITUDE = 'latitude';
    public const OPENING_HOURS = 'opening_hours';
    public const IS_DISABLED = 'is_disabled';

    public const TYPE_LOCKER = 1;
    public const TYPE_PARCELSHOP = 2;
    public const TYPE_ROBOT = 3;

    /**
     * Public method
     *
     * @return int
     */
    public function getPickupPointId();

    /**
     * Public method
     *
     * @param int $pickupPointId
     * @return PickupPointInterface
     */
    public function setPickupPointId($pickupPointId);

    /**
     * Public method
     *
     * @return string
     */
    public function getApiId();

    /**
     * Public method
     *
     * @param string $apiId
     * @return PickupPointInterface
     */
    public function setApiId($apiId);

    /**
     * Public method
     *
     * @return int
     */
    public function getType();

    /**
     * Public method
     *
     * @param int $type
     * @return PickupPointInterface
     */
    public function setType($type);

    /**
     * Public method
     *
     * @return string
     */
    public function getCompany();

    /**
     * Public method
     *
     * @param string $company
     * @return PickupPointInterface
     */
    public function setCompany($company);

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
     * @return PickupPointInterface
     */
    public function setCountry($country);

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
     * @return PickupPointInterface
     */
    public function setCity($city);

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
     * @return PickupPointInterface
     */
    public function setPostcode($postcode);

    /**
     * Public method
     *
     * @return string
     */
    public function getStreet();

    /**
     * Public method
     *
     * @param string $street
     * @return PickupPointInterface
     */
    public function setStreet($street);

    /**
     * Public method
     *
     * @return string
     */
    public function getEmail();

    /**
     * Public method
     *
     * @param string $email
     * @return PickupPointInterface
     */
    public function setEmail($email);

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
     * @return PickupPointInterface
     */
    public function setPhone($phone);

    /**
     * Public method
     *
     * @return string
     */
    public function getLongitude();

    /**
     * Public method
     *
     * @param string $longitude
     * @return PickupPointInterface
     */
    public function setLongitude($longitude);

    /**
     * Public method
     *
     * @return string
     */
    public function getLatitude();

    /**
     * Public method
     *
     * @param string $latitude
     * @return PickupPointInterface
     */
    public function setLatitude($latitude);

    /**
     * Public method
     *
     * @return array
     */
    public function getOpeningHours();

    /**
     * Public method
     *
     * @param array $openingHours
     * @return PickupPointInterface
     */
    public function setOpeningHours($openingHours);

    /**
     * Public method
     *
     * @return bool
     */
    public function getIsDisabled();

    /**
     * Public method
     *
     * @param bool $isDisabled
     * @return PickupPointInterface
     */
    public function setIsDisabled($isDisabled);
}
