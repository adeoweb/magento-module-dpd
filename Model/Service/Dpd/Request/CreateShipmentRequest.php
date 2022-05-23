<?php

namespace AdeoWeb\Dpd\Model\Service\Dpd\Request;

use AdeoWeb\Dpd\Helper\Utils;
use AdeoWeb\Dpd\Model\Service\RequestInterface;

/**
 * @codeCoverageIgnore
 */
class CreateShipmentRequest extends AbstractRequest implements RequestInterface
{
    const ENDPOINT = 'createShipment_';

    protected $params = [
        'name1',
        'name2',
        'street',
        'city',
        'country',
        'pcode',
        'num_of_parcel',
        'weight',
        'parcel_type',
        'parcelshop_id',
        'phone',
        'idm_sms_number',
        'email',
        'order_number',
        'order_number1',
        'order_number2',
        'order_number3',
        'parcel_number',
        'remark',
        'cod_amount',
        'cod_purpose',
        'id_check_id',
        'id_check_name',
        'dnote_reference',
        'predict',
        'timeframe_from',
        'timeframe_to',
        'fetchGsPUDOpoint'
    ];

    protected $requiredParams = [
        'name1',
        'street',
        'city',
        'country',
        'pcode',
        'num_of_parcel',
        'parcel_type',
        'phone'
    ];

    /**
     * @param string $name1
     * @return CreateShipmentRequest
     */
    public function setName1($name1)
    {
        return $this->setData('name1', $name1, 40);
    }

    /**
     * @param string $name2
     * @return CreateShipmentRequest
     */
    public function setName2($name2)
    {
        return $this->setData('name2', $name2, 40);
    }

    /**
     * @param string $street
     * @return CreateShipmentRequest
     */
    public function setStreet($street)
    {
        return $this->setData('street', $street, 40);
    }

    /**
     * @param string $city
     * @return CreateShipmentRequest
     */
    public function setCity($city)
    {
        return $this->setData('city', $city, 40);
    }

    /**
     * @param string $country
     * @return CreateShipmentRequest
     */
    public function setCountry($country)
    {
        return $this->setData('country', $country, 2);
    }

    /**
     * @param string $postcode
     * @return CreateShipmentRequest
     */
    public function setPostcode($postcode)
    {
        return $this->setData('pcode', $postcode, 9);
    }

    /**
     * @param int $numOfParcel
     * @return CreateShipmentRequest
     */
    public function setNumOfParcel($numOfParcel)
    {
        return $this->setData('num_of_parcel', $numOfParcel, 2);
    }

    /**
     * @param string $weight
     * @return CreateShipmentRequest
     */
    public function setWeight($weight)
    {
        return $this->setData('weight', $weight, 6);
    }

    /**
     * @param string $parcelType
     * @return CreateShipmentRequest
     */
    public function setParcelType($parcelType)
    {
        return $this->setData('parcel_type', $parcelType, 30);
    }

    /**
     * @param string $parcelshopId
     * @return CreateShipmentRequest
     */
    public function setParcelshopId($parcelshopId)
    {
        return $this->setData('parcelshop_id', $parcelshopId, 8);
    }

    /**
     * @param string $phone
     * @return CreateShipmentRequest
     */
    public function setPhone($phone)
    {
        return $this->setData('phone', $phone, 50);
    }

    /**
     * @param string $idmSmsNumber
     * @return CreateShipmentRequest
     */
    public function setIdmSmsNumber($idmSmsNumber)
    {
        return $this->setData('idm_sms_number', $idmSmsNumber, 20);
    }

    /**
     * @param string $email
     * @return CreateShipmentRequest
     */
    public function setEmail($email)
    {
        return $this->setData('email', $email, 100);
    }

    /**
     * @param string $orderNumber
     * @return CreateShipmentRequest
     */
    public function setOrderNumber($orderNumber)
    {
        return $this->setData('order_number', $orderNumber, 20);
    }

    /**
     * @param string $orderNumber1
     * @return CreateShipmentRequest
     */
    public function setOrderNumber1($orderNumber1)
    {
        return $this->setData('order_number1', $orderNumber1, 20);
    }

    /**
     * @param string $orderNumber2
     * @return CreateShipmentRequest
     */
    public function setOrderNumber2($orderNumber2)
    {
        return $this->setData('order_number2', $orderNumber2, 20);
    }

    /**
     * @param string $orderNumber3
     * @return CreateShipmentRequest
     */
    public function setOrderNumber3($orderNumber3)
    {
        return $this->setData('order_number3', $orderNumber3, 20);
    }

    /**
     * @param string $parcelNumber
     * @return CreateShipmentRequest
     */
    public function setParcelNumber($parcelNumber)
    {
        return $this->setData('parcel_number', $parcelNumber, 20);
    }

    /**
     * @param string $remark
     * @return CreateShipmentRequest
     */
    public function setRemark($remark)
    {
        return $this->setData('remark', $remark, 45);
    }

    /**
     * @param string $codAmount
     * @return CreateShipmentRequest
     */
    public function setCodAmount($codAmount)
    {
        return $this->setData('cod_amount', $codAmount, 10);
    }

    /**
     * @param string $codPurpose
     * @return CreateShipmentRequest
     */
    public function setCodPurpose($codPurpose)
    {
        return $this->setData('cod_purpose', $codPurpose, 50);
    }

    /**
     * @param string $idCheckId
     * @return CreateShipmentRequest
     */
    public function setIdCheckId($idCheckId)
    {
        return $this->setData('id_check_id', $idCheckId, 40);
    }

    /**
     * @param string $idCheckName
     * @return CreateShipmentRequest
     */
    public function setIdCheckName($idCheckName)
    {
        return $this->setData('id_check_name', $idCheckName, 40);
    }

    /**
     * @param string $dnoteReference
     * @return CreateShipmentRequest
     */
    public function setDnoteReference($dnoteReference)
    {
        return $this->setData('dnote_reference', $dnoteReference, 49);
    }

    /**
     * @param string $predict
     * @return CreateShipmentRequest
     */
    public function setPredict($predict)
    {
        return $this->setData('predict', $predict, 1);
    }

    /**
     * @param string $timeframeFrom
     * @return CreateShipmentRequest
     */
    public function setTimeframeFrom($timeframeFrom)
    {
        return $this->setData('timeframe_from', $timeframeFrom);
    }

    /**
     * @param string $timeframeTo
     * @return CreateShipmentRequest
     */
    public function setTimeframeTo($timeframeTo)
    {
        return $this->setData('timeframe_to', $timeframeTo);
    }

    /**
     * @param string|int|boolean $value
     * @return CreateShipmentRequest
     */
    public function setFetchAllByCountryFlag($value)
    {
        return $this->setData('fetchGsPUDOpoint', (int)$value);
    }
}
