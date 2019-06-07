<?php

namespace AdeoWeb\Dpd\Model\Service\Dpd\Request;

use AdeoWeb\Dpd\Model\Service\RequestInterface;

/**
 * Class PickupOrderSaveRequest
 * @codeCoverageIgnore
 */
class PickupOrderSaveRequest extends AbstractRequest implements RequestInterface
{
    const ENDPOINT = 'pickupOrderSave_';

    protected $params = [
        'orderNr',
        'payerId',
        'senderName',
        'senderAddress',
        'senderCity',
        'senderCountry',
        'senderPostalCode',
        'senderAddAddress',
        'senderContact',
        'senderPhone',
        'senderWorkUntil',
        'pickupTime',
        'weight',
        'parcelsCount',
        'palletsCount',
        'nonStandard'
    ];

    protected $requiredParams = [
        'orderNr',
        'senderAddress',
        'senderCity',
        'senderCountry',
        'senderPostalCode',
        'senderContact',
        'senderPhone',
        'senderWorkUntil',
        'pickupTime',
        'weight',
        'parcelsCount'
    ];

    /**
     * @param string $orderNr
     * @return PickupOrderSaveRequest
     */
    public function setOrderNr($orderNr)
    {
        return $this->setData('orderNr', $orderNr, 100);
    }

    /**
     * @param string $payerId
     * @return PickupOrderSaveRequest
     */
    public function setPayerId($payerId)
    {
        return $this->setData('payerId', $payerId);
    }

    /**
     * @param string $senderName
     * @return PickupOrderSaveRequest
     */
    public function setSenderName($senderName)
    {
        return $this->setData('senderName', $senderName, 100);
    }

    /**
     * @param string $senderAddress
     * @return PickupOrderSaveRequest
     */
    public function setSenderAddress($senderAddress)
    {
        return $this->setData('senderAddress', $senderAddress, 100);
    }

    /**
     * @param string $senderCity
     * @return PickupOrderSaveRequest
     */
    public function setSenderCity($senderCity)
    {
        return $this->setData('senderCity', $senderCity, 100);
    }

    /**
     * @param string $senderCountry
     * @return PickupOrderSaveRequest
     */
    public function setSenderCountry($senderCountry)
    {
        return $this->setData('senderCountry', $senderCountry, 2);
    }

    /**
     * @param string $senderPostalCode
     * @return PickupOrderSaveRequest
     */
    public function setSenderPostalCode($senderPostalCode)
    {
        return $this->setData('senderPostalCode', $senderPostalCode, 10);
    }

    /**
     * @param string $senderAddAddress
     * @return PickupOrderSaveRequest
     */
    public function setSenderAddAddress($senderAddAddress)
    {
        return $this->setData('senderAddAddress', $senderAddAddress,100);
    }

    /**
     * @param string $senderContact
     * @return PickupOrderSaveRequest
     */
    public function setSenderContact($senderContact)
    {
        return $this->setData('senderContact', $senderContact, 100);
    }

    /**
     * @param string $senderPhone
     * @return PickupOrderSaveRequest
     */
    public function setSenderPhone($senderPhone)
    {
        return $this->setData('senderPhone', $senderPhone, 100);
    }

    /**
     * @param string $senderWorkUntil
     * @return PickupOrderSaveRequest
     */
    public function setSenderWorkUntil($senderWorkUntil)
    {
        return $this->setData('senderWorkUntil', $senderWorkUntil);
    }

    /**
     * @param string $pickupTime
     * @return PickupOrderSaveRequest
     */
    public function setPickupTime($pickupTime)
    {
        return $this->setData('pickupTime', $pickupTime);
    }

    /**
     * @param string $weight
     * @return PickupOrderSaveRequest
     */
    public function setWeight($weight)
    {
        return $this->setData('weight', $weight, 10);
    }

    /**
     * @param string $parcelsCount
     * @return PickupOrderSaveRequest
     */
    public function setParcelsCount($parcelsCount)
    {
        return $this->setData('parcelsCount', $parcelsCount, 5);
    }

    /**
     * @param string $palletsCount
     * @return PickupOrderSaveRequest
     */
    public function setPalletsCount($palletsCount)
    {
        return $this->setData('palletsCount', $palletsCount, 5);
    }

    /**
     * @param string $comment
     * @return PickupOrderSaveRequest
     */
    public function setComment($comment)
    {
        return $this->setData('nonStandard', $comment, 100);
    }

    /**
     * {@inheritDoc}
     */
    public function isFile()
    {
        return true;
    }
}