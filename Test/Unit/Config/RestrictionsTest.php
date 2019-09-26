<?php

namespace AdeoWeb\Dpd\Test\Unit\Config;

use AdeoWeb\Dpd\Config\Restrictions;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use PHPUnit\Framework\MockObject\MockObject;

class RestrictionsTest extends AbstractTest
{
    /**
     * @var MockObject|\AdeoWeb\Dpd\Helper\Config\Serializer
     */
    private $serializerMock;

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

        $this->serializerMock = $this->createMock(\AdeoWeb\Dpd\Helper\Config\Serializer::class);

        $sortProcessorMock = $this->createMock(\AdeoWeb\Dpd\Model\Adminhtml\System\Config\WeightPrice\SortProcessor::class);
        $sortProcessorMock->expects($this->any())
            ->method('processAsc')
            ->will($this->returnValueMap([
                [[['weight' => 10, 'price' => 10]], [['weight' => 10, 'price' => 10]]]
            ]));

        $this->subject = $this->objectManager->getObject(Restrictions::class, [
            'scopeConfig' => $this->scopeConfigMock,
            'sortProcessor' => $sortProcessorMock,
            'serializer' => $this->serializerMock
        ]);
    }

    public function testGetByCountryWithInvalidConfigSettings()
    {
        $this->scopeConfigMock->expects($this->atleastOnce())
            ->method('getValue')
            ->with('carriers/dpd/classic/restrictions', 'website')
            ->willReturn('s:4:"test";');

        $this->serializerMock->expects($this->any())
            ->method('unserialize')
            ->will($this->returnValueMap([['s:4:"test";', 'test']]));


        $this->assertNull($this->subject->getByCountryWeight('test', 10));
    }

    public function testGetByCountryWithIncorrectCountryCode()
    {
        $this->scopeConfigMock->expects($this->atleastOnce())
            ->method('getValue')
            ->with('carriers/dpd/classic/restrictions', 'website')
            ->willReturn('a:2:{i:0;a:2:{s:7:"country";s:2:"US";s:5:"price";i:10;}i:1;a:2:{s:7:"country";s:2:"CA";s:5:"price";i:15;}}');

        $this->serializerMock->expects($this->any())->method('unserialize')->will($this->returnValueMap([
            [
                'a:2:{i:0;a:2:{s:7:"country";s:2:"US";s:5:"price";i:10;}i:1;a:2:{s:7:"country";s:2:"CA";s:5:"price";i:15;}}',
                [['country' => 'US', 'price' => 10], ['country' => 'CA', 'price' => 15]]
            ]
        ]));

        $result = $this->subject->getByCountryWeight('LT', 8);
        $expectedValue = null;

        $this->assertEquals($expectedValue, $result);
    }

    public function testGetByCountry()
    {
        $this->scopeConfigMock->expects($this->atleastOnce())
            ->method('getValue')
            ->with('carriers/dpd/classic/restrictions', 'website')
            ->willReturn('a:2:{i:0;a:2:{s:7:"country";s:2:"US";s:5:"price";i:10;}i:1;a:2:{s:7:"country";s:2:"CA";s:5:"price";i:15;}}');

        $this->serializerMock->expects($this->any())->method('unserialize')->will($this->returnValueMap([
            [
                'a:2:{i:0;a:2:{s:7:"country";s:2:"US";s:5:"price";i:10;}i:1;a:2:{s:7:"country";s:2:"CA";s:5:"price";i:15;}}',
                [
                    ['country' => 'US', 'weight_price' => [['weight' => 10, 'price' => 10]]],
                    ['country' => 'CA', 'weight_price' => [['weight' => 10, 'price' => 8]]]
                ]
            ]
        ]));

        $result = $this->subject->getByCountryWeight('US', 12);
        $expectedValue = 10;

        $this->assertEquals($expectedValue, $result);
    }
}
