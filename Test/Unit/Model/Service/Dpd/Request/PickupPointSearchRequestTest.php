<?php

namespace Model\Service\Dpd\Request;

use AdeoWeb\Dpd\Model\Service\Dpd\Request\PickupPointSearchRequest;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use Magento\Framework\Exception\LocalizedException;

class PickupPointSearchRequestTest extends AbstractTest
{
    /**
     * @var PickupPointSearchRequest
     */
    private $subject;

    public function setUp(): void
    {
        parent::setUp();

        $this->subject = $this->objectManager->getObject(PickupPointSearchRequest::class);
    }

    public function testGetParamsWithException()
    {
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('DPD Request is not valid. Required parameter "country" is missing');

        return $this->subject->getParams();
    }

    public function testGetParams()
    {
        $this->subject->setId(1);
        $this->subject->setCompany('TestCompany');
        $this->subject->setStreet(null);
        $this->subject->setCity('TestCity');
        $this->subject->setCountry('US');
        $this->subject->setPostcode('12345');
        $this->subject->setFetchAllByCountryFlag(1);
        $this->subject->setRetrieveOpeningHoursFlag(1);

        $result = $this->subject->getParams();
        $expectedResult = [
            'id' => 1,
            'company' => 'TestCompany',
            'country' => 'US',
            'city' => 'TestCity',
            'pcode' => '12345',
            'fetchGsPUDOpoint' => 1,
            'retrieveOpeningHours' => 1,
        ];

        $this->assertEquals($expectedResult, $result);
    }
}
