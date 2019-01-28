<?php

namespace AdeoWeb\Dpd\Model\Service\Dpd\Request;

use AdeoWeb\Dpd\Model\Service\RequestInterface;

/**
 * Class PickupPointSearchRequest
 */
class PickupPointSearchRequest extends AbstractRequest implements RequestInterface
{
    const ENDPOINT = 'parcelShopSearch_';

    protected $params = [
        'id',
        'company',
        'street',
        'city',
        'country',
        'pcode',
        'fetchGsPUDOpoint',
        'retrieveOpeningHours'
    ];

    protected $requiredParams = [
        'country',
        'fetchGsPUDOpoint'
    ];

    /**
     * @param string $id
     * @return PickupPointSearchRequest
     */
    public function setId($id)
    {
        return $this->setData('id', $id, 20);
    }

    /**
     * @param string $company
     * @return PickupPointSearchRequest
     */
    public function setCompany($company)
    {
        return $this->setData('company', $company, 40);
    }

    /**
     * @param string $street
     * @return PickupPointSearchRequest
     */
    public function setStreet($street)
    {
        return $this->setData('street', $street, 40);
    }

    /**
     * @param string $city
     * @return PickupPointSearchRequest
     */
    public function setCity($city)
    {
        return $this->setData('city', $city, 40);
    }

    /**
     * @param string $country
     * @return PickupPointSearchRequest
     */
    public function setCountry($country)
    {
        return $this->setData('country', $country, 2);
    }

    /**
     * @param string $postcode
     * @return PickupPointSearchRequest
     */
    public function setPostcode($postcode)
    {
        return $this->setData('pcode', $postcode, 5);
    }

    /**
     * @param string|int|boolean $value
     * @return PickupPointSearchRequest
     */
    public function setFetchAllByCountryFlag($value)
    {
        return $this->setData('fetchGsPUDOpoint', (int)$value);
    }

    /**
     * @param string|int|boolean $value
     * @return PickupPointSearchRequest
     */
    public function setRetrieveOpeningHoursFlag($value)
    {
        return $this->setData('retrieveOpeningHours', (int)$value);
    }
}