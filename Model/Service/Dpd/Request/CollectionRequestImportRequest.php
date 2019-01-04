<?php

namespace AdeoWeb\Dpd\Model\Service\Dpd\Request;

use AdeoWeb\Dpd\Model\Service\RequestInterface;

class CollectionRequestImportRequest extends AbstractRequest implements RequestInterface
{
    const ENDPOINT = 'crImport_';

    protected $params = [
        'cname',
        'cname1',
        'cname2',
        'cname3',
        'cstreet',
        'ccountry',
        'cpostal',
        'ccity',
        'cphone',
        'cemail',
        'info1',
        'info2',
        'rname',
        'rname2',
        'rstreet',
        'rpostal',
        'rcountry',
        'rcity',
        'rphone',
        'remail'
    ];

    protected $requiredParams = [
        'cname',
        'cstreet',
        'ccountry',
        'cpostal',
        'ccity',
        'info1',
        'rname',
        'rstreet',
        'rpostal',
        'rcountry',
        'rcity'
    ];

    /**
     * @param string $pickupName
     * @return CollectionRequestImportRequest
     */
    public function setPickupName($pickupName)
    {
        return $this->setData('cname', $pickupName, 35);
    }

    /**
     * @param string $pickupName1
     * @return CollectionRequestImportRequest
     */
    public function setPickupName1($pickupName1)
    {
        return $this->setData('cname1', $pickupName1, 35);
    }

    /**
     * @param string $pickupName2
     * @return CollectionRequestImportRequest
     */
    public function setPickupName2($pickupName2)
    {
        return $this->setData('cname2', $pickupName2, 35);
    }

    /**
     * @param string $pickupName3
     * @return CollectionRequestImportRequest
     */
    public function setPickupName3($pickupName3)
    {
        return $this->setData('cname3', $pickupName3, 35);
    }

    /**
     * @param string $pickupStreet
     * @return CollectionRequestImportRequest
     */
    public function setPickupStreet($pickupStreet)
    {
        return $this->setData('cstreet', $pickupStreet, 35);
    }

    /**
     * @param string $pickupCountry
     * @return CollectionRequestImportRequest
     */
    public function setPickupCountry($pickupCountry)
    {
        return $this->setData('ccountry', $pickupCountry, 30);
    }

    /**
     * @param string $pickupPostCode
     * @return CollectionRequestImportRequest
     */
    public function setPickupPostCode($pickupPostCode)
    {
        return $this->setData('cpostal', $pickupPostCode, 8);
    }

    /**
     * @param string $pickupCity
     * @return CollectionRequestImportRequest
     */
    public function setPickupCity($pickupCity)
    {
        return $this->setData('ccity', $pickupCity, 25);
    }

    /**
     * @param string $pickupPhone
     * @return CollectionRequestImportRequest
     */
    public function setPickupPhone($pickupPhone)
    {
        return $this->setData('cphone', $pickupPhone, 20);
    }

    /**
     * @param string $pickupEmail
     * @return CollectionRequestImportRequest
     */
    public function setPickupEmail($pickupEmail)
    {
        return $this->setData('cemail', $pickupEmail, 30);
    }

    /**
     * @param string $info1
     * @return CollectionRequestImportRequest
     */
    public function setInfo1($info1)
    {
        return $this->setData('info1', $info1, 30);
    }

    /**
     * @param string $info2
     * @return CollectionRequestImportRequest
     */
    public function setInfo2($info2)
    {
        return $this->setData('info2', $info2, 30);
    }

    /**
     * @param string $recipientName
     * @return CollectionRequestImportRequest
     */
    public function setRecipientName($recipientName)
    {
        return $this->setData('rname', $recipientName, 35);
    }

    /**
     * @param string $recipientName2
     * @return CollectionRequestImportRequest
     */
    public function setRecipientName2($recipientName2)
    {
        return $this->setData('rname2', $recipientName2, 35);
    }

    /**
     * @param string $recipientStreet
     * @return CollectionRequestImportRequest
     */
    public function setRecipientStreet($recipientStreet)
    {
        return $this->setData('rstreet', $recipientStreet, 35);
    }

    /**
     * @param string $recipientPostCode
     * @return CollectionRequestImportRequest
     */
    public function setRecipientPostCode($recipientPostCode)
    {
        return $this->setData('rpostal', $recipientPostCode, 8);
    }

    /**
     * @param string $recipientCountry
     * @return CollectionRequestImportRequest
     */
    public function setRecipientCountry($recipientCountry)
    {
        return $this->setData('rcountry', $recipientCountry, 30);
    }

    /**
     * @param string $recipientCity
     * @return CollectionRequestImportRequest
     */
    public function setRecipientCity($recipientCity)
    {
        return $this->setData('rcity', $recipientCity, 25);
    }

    /**
     * @param string $recipientPhone
     * @return CollectionRequestImportRequest
     */
    public function setRecipientPhone($recipientPhone)
    {
        return $this->setData('rphone', $recipientPhone, 20);
    }

    /**
     * @param string $recipientEmail
     * @return CollectionRequestImportRequest
     */
    public function setRecipientEmail($recipientEmail)
    {
        return $this->setData('remail', $recipientEmail, 30);
    }

    /**
     * {@inheritDoc}
     */
    public function isFile()
    {
        return true;
    }
}