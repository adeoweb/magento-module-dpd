<?php

namespace AdeoWeb\Dpd\Test\Unit\Model\Carrier\Validator;

use AdeoWeb\Dpd\Model\Carrier\Validator\Country;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use PHPUnit\Framework\MockObject\MockObject;

class CountryTest extends AbstractTest
{
    /**
     * @var Country
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $scopeConfigMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);

        $this->subject = $this->objectManager->getObject(Country::class, [
            'scopeConfig' => $this->scopeConfigMock
        ]);
    }

    public function testValidateWithMissingDataException()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid validator data.');

        return $this->subject->validate([]);
    }

    public function testValidateWithInvalidCountry()
    {
        $requestMock = $this->createPartialMock(DataObject::class, []);

        $requestMock->setDestCountryId('CA');

        $this->scopeConfigMock->expects($this->atleastOnce())
            ->method('getValue')
            ->withConsecutive(
                ['carriers/dpd/classic/sallowspecific'],
                ['carriers/dpd/classic/specificcountry'],
                ['carriers/dpd/classic/specificcountry']
            )
            ->willReturn(true, 'US', 'US');

        $result = $this->subject->validate([
            'method_code' => 'classic',
            'request' => $requestMock
        ]);

        $this->assertFalse($result);
    }

    public function testValidate()
    {
        $requestMock = $this->createPartialMock(DataObject::class, []);

        $requestMock->setDestCountryId('US');

        $this->scopeConfigMock->expects($this->atleastOnce())
            ->method('getValue')
            ->withConsecutive(
                ['carriers/dpd/classic/sallowspecific'],
                ['carriers/dpd/classic/specificcountry'],
                ['carriers/dpd/classic/specificcountry']
            )
            ->willReturn(true, 'US', 'US');

        $result = $this->subject->validate([
            'method_code' => 'classic',
            'request' => $requestMock
        ]);

        $this->assertTrue($result);
    }
}
