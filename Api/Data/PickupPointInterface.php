<?php

namespace AdeoWeb\Dpd\Api\Data;

interface PickupPointInterface
{
    const PICKUP_POINT_ID = 'pickup_point_id';
    const API_ID = 'api_id';
    const TYPE = 'type';
    const COMPANY = 'company';
    const COUNTRY = 'country';
    const CITY = 'city';
    const POSTCODE = 'postcode';
    const STREET = 'street';
    const EMAIL = 'email';
    const PHONE = 'phone';
    const LONGITUDE = 'longitude';
    const LATITUDE = 'latitude';

    const TYPE_LOCKER = 1;
    const TYPE_PARCELSHOP = 2;
    const TYPE_ROBOT = 3;

    /**
     * @return int
     */
    public function getPickupPointId();

    /**
     * @param int $pickupPointId
     * @return PickupPointInterface
     */
    public function setPickupPointId($pickupPointId);

    /**
     * @return string
     */
    public function getApiId();

    /**
     * @param string $apiId
     * @return PickupPointInterface
     */
    public function setApiId($apiId);

    /**
     * @return int
     */
    public function getType();

    /**
     * @param int $type
     * @return PickupPointInterface
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getCompany();

    /**
     * @param string $company
     * @return PickupPointInterface
     */
    public function setCompany($company);

    /**
     * @return string
     */
    public function getCountry();

    /**
     * @param string $country
     * @return PickupPointInterface
     */
    public function setCountry($country);

    /**
     * @return string
     */
    public function getCity();

    /**
     * @param string $city
     * @return PickupPointInterface
     */
    public function setCity($city);

    /**
     * @return string
     */
    public function getPostcode();

    /**
     * @param string $postcode
     * @return PickupPointInterface
     */
    public function setPostcode($postcode);

    /**
     * @return string
     */
    public function getStreet();

    /**
     * @param string $street
     * @return PickupPointInterface
     */
    public function setStreet($street);

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @param string $email
     * @return PickupPointInterface
     */
    public function setEmail($email);

    /**
     * @return string
     */
    public function getPhone();

    /**
     * @param string $phone
     * @return PickupPointInterface
     */
    public function setPhone($phone);

    /**
     * @return string
     */
    public function getLongitude();

    /**
     * @param string $longitude
     * @return PickupPointInterface
     */
    public function setLongitude($longitude);

    /**
     * @return string
     */
    public function getLatitude();

    /**
     * @param string $latitude
     * @return PickupPointInterface
     */
    public function setLatitude($latitude);
}