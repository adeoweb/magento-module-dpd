<?php

namespace AdeoWeb\Dpd\Test\Unit\Model\Provider\PickupPoint;

use AdeoWeb\Dpd\Model\Provider\PickupPoint\AllowedCountries;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use Magento\Framework\App\Config\ScopeConfigInterface;

class AllowedCountriesTest extends AbstractTest
{
    /**
     * @var AllowedCountries
     */
    private $subject;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $scopeConfigMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);

        $this->subject = $this->objectManager->getObject(AllowedCountries::class, [
            'scopeConfig' => $this->scopeConfigMock
        ]);
    }

    public function testGetWithAllCountriesAllowed()
    {
        $this->scopeConfigMock->method('getValue')
            ->withConsecutive(['carriers/dpd/pickup/sallowspecific'], ['general/country/eu_countries'])
            ->willReturnOnConsecutiveCalls('0', 'LT,LV,EE');

        $expectedResult = ['LT', 'LV', 'EE'];
        $result = $this->subject->get();

        $this->assertEquals($expectedResult, $result);
    }

    public function testGet()
    {
        $this->scopeConfigMock->method('getValue')
            ->withConsecutive(['carriers/dpd/pickup/sallowspecific'], ['carriers/dpd/pickup/specificcountry'])
            ->willReturnOnConsecutiveCalls('1', 'LT,LV,EE');

        $expectedResult = ['LT', 'LV', 'EE'];
        $result = $this->subject->get();

        $this->assertEquals($expectedResult, $result);
    }
}
