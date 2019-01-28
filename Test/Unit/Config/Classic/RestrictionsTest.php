<?php

namespace AdeoWeb\Dpd\Test\Unit\Config\Classic;

use AdeoWeb\Dpd\Config\Classic\Restrictions;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use PHPUnit\Framework\MockObject\MockObject;

class RestrictionsTest extends AbstractTest
{
    /**
     * @var Restrictions
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $scopeConfigMock;

    public function setUp()
    {
        parent::setUp();

        $this->scopeConfigMock = $this->createMock(\Magento\Framework\App\Config\ScopeConfigInterface::class);

        $serializer = $this->objectManager->getObject(\AdeoWeb\Dpd\Helper\Config\Serializer::class);

        $this->subject = $this->objectManager->getObject(Restrictions::class, [
            'scopeConfig' => $this->scopeConfigMock,
            'serializer' => $serializer
        ]);
    }

    public function testGetByCountryWithInvalidConfigSettings()
    {
        $this->scopeConfigMock->expects($this->atleastOnce())
            ->method('getValue')
            ->with('carriers/dpd/classic/restrictions', 'website')
            ->willReturn('s:4:"test";');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid method configuration');

        return $this->subject->getByCountry('US');
    }

    public function testGetByCountryWithIncorrectCountryCode()
    {
        $this->scopeConfigMock->expects($this->atleastOnce())
            ->method('getValue')
            ->with('carriers/dpd/classic/restrictions', 'website')
            ->willReturn('a:2:{i:0;a:2:{s:7:"country";s:2:"US";s:5:"price";i:10;}i:1;a:2:{s:7:"country";s:2:"CA";s:5:"price";i:15;}}');

        $result = $this->subject->getByCountry('LT');
        $expectedValue = null;

        $this->assertEquals($expectedValue, $result);
    }

    public function testGetByCountry()
    {
        $this->scopeConfigMock->expects($this->atleastOnce())
            ->method('getValue')
            ->with('carriers/dpd/classic/restrictions', 'website')
            ->willReturn('a:2:{i:0;a:2:{s:7:"country";s:2:"US";s:5:"price";i:10;}i:1;a:2:{s:7:"country";s:2:"CA";s:5:"price";i:15;}}');

        $result = $this->subject->getByCountry('US');
        $expectedValue =['country' => 'US', 'price' => 10];

        $this->assertEquals($expectedValue, $result);
    }
}