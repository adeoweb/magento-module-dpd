<?php

namespace AdeoWeb\Dpd\Test\Unit\Model\Config\Source;

use AdeoWeb\Dpd\Model\Config\Source\EuropeanCountries;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use Magento\Directory\Model\Country;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

class EuropeanCountriesTest extends AbstractTest
{
    /**
     * @var EuropeanCountries
     */
    private $subject;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $scopeConfigMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $countryCollectionFactoryMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->countryCollectionFactoryMock = $this->createMock(CollectionFactory::class);

        $this->subject = $this->objectManager->getObject(EuropeanCountries::class, [
            'scopeConfig' => $this->scopeConfigMock,
            'countryCollectionFactory' => $this->countryCollectionFactoryMock,
        ]);
    }

    public function testToOptionArrayWithNoneEuropeanCountries()
    {
        $this->scopeConfigMock->method('getValue')
            ->with('general/country/eu_countries')
            ->willReturn('');

        $result = $this->subject->toOptionArray();
        $expectedResult = [['value' => '', 'label' => __('--Please Select--')]];

        $this->assertEquals($expectedResult, $result);
    }

    public function testToOptionArray()
    {
        $this->scopeConfigMock->method('getValue')
            ->with('general/country/eu_countries')
            ->willReturn('LT,BE');

        $countryCollectionMock = $this->createMock(\Magento\Directory\Model\ResourceModel\Country\Collection::class);

        $this->countryCollectionFactoryMock->method('create')
            ->willReturn($countryCollectionMock);

        $countryLt = $this->createMock(Country::class);
        $countryBe = $this->createMock(Country::class);

        $countryCollectionMock->method('getItems')
            ->willReturn([$countryLt, $countryBe]);

        $countryLt->method('__call')
            ->with('getCountryId')
            ->willReturn('LT');
        $countryLt->method('getName')
            ->willReturn('Lithuania');

        $countryBe->method('__call')
            ->with('getCountryId')
            ->willReturn('BE');
        $countryBe->method('getName')
            ->willReturn('Belgium');

        $result = $this->subject->toOptionArray();
        $expectedResult = [
            0 =>
                [
                    'value' => '',
                    'label' => __('--Please Select--'),
                ],
            1 =>
                [
                    'value' => 'LT',
                    'label' => 'Lithuania',
                ],
            2 =>
                [
                    'value' => 'BE',
                    'label' => 'Belgium',
                ],
        ];

        $this->assertEquals($expectedResult, $result);
    }
}
