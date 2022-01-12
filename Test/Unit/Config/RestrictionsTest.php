<?php

declare(strict_types=1);

namespace AdeoWeb\Dpd\Test\Unit\Config;

use AdeoWeb\Dpd\Config\Restrictions;
use AdeoWeb\Dpd\Helper\Config\Serializer;
use AdeoWeb\Dpd\Model\Adminhtml\System\Config\WeightPrice\SortProcessor;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use Magento\Framework\App\Config\ScopeConfigInterface;
use PHPUnit\Framework\MockObject\MockObject;

class RestrictionsTest extends AbstractTest
{
    /**
     * @var MockObject|Serializer
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

    public function setUp(): void
    {
        parent::setUp();

        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);

        $this->serializerMock = $this->createMock(Serializer::class);

        $sortProcessorMock = $this->createMock(SortProcessor::class);
        $sortProcessorMock->expects($this->any())
            ->method('processAsc')
            ->will($this->returnValueMap([
                [[['weight' => 10, 'price' => 10]], [['weight' => 10, 'price' => 10]]],
                [
                    ['_empty', ['weight' => 15, 'price' => 15], ['weight' => 10, 'price' => 10]],
                    ['_empty', ['weight' => 10, 'price' => 10], ['weight' => 15, 'price' => 15]]
                ]
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

    public function testGetByCountryWeightNoWeightPrice()
    {
        $this->scopeConfigMock->expects($this->atleastOnce())
            ->method('getValue')
            ->with('carriers/dpd/classic/restrictions', 'website')
            ->willReturn('a:2:{i:0;a:2:{s:7:"country";s:2:"US";s:5:"price";i:10;}i:1;a:2:{s:7:"country";s:2:"CA";s:5:"price";i:15;}}');

        $this->serializerMock->expects($this->any())->method('unserialize')->will($this->returnValueMap([
            [
                'a:2:{i:0;a:2:{s:7:"country";s:2:"US";s:5:"price";i:10;}i:1;a:2:{s:7:"country";s:2:"CA";s:5:"price";i:15;}}',
                [
                    ['country' => 'US', 'weight_price' => []],
                    ['country' => 'CA', 'weight_price' => [['weight' => 10, 'price' => 8]]]
                ]
            ]
        ]));

        $this->assertNull($this->subject->getByCountryWeight('US', 12));
    }

    public function testGetByCountryWeightHeavierConfig()
    {
        $this->scopeConfigMock->expects($this->atleastOnce())
            ->method('getValue')
            ->with('carriers/dpd/classic/restrictions', 'website')
            ->willReturn('a:2:{i:0;a:2:{s:7:"country";s:2:"US";s:5:"price";i:10;}i:1;a:2:{s:7:"country";s:2:"CA";s:5:"price";i:15;}}');

        $this->serializerMock->expects($this->any())->method('unserialize')->will($this->returnValueMap([
            [
                'a:2:{i:0;a:2:{s:7:"country";s:2:"US";s:5:"price";i:10;}i:1;a:2:{s:7:"country";s:2:"CA";s:5:"price";i:15;}}',
                [
                    [
                        'country' => 'US',
                        'weight_price' => ['_empty', ['weight' => 15, 'price' => 15], ['weight' => 10, 'price' => 10]]
                    ],
                ]
            ]
        ]));

        $this->assertEquals(10, $this->subject->getByCountryWeight('US', 12));
    }
}
