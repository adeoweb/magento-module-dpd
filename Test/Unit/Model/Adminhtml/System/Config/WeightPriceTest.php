<?php

declare(strict_types=1);

namespace AdeoWeb\Dpd\Test\Unit\Model\Adminhtml\System\Config;

use AdeoWeb\Dpd\Model\Adminhtml\System\Config\WeightPrice;
use AdeoWeb\Dpd\Model\Adminhtml\System\Config\WeightPrice\SortProcessor;
use Magento\Framework\DataObject;
use Magento\Framework\Escaper;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class WeightPriceTest extends TestCase
{
    /**
     * @var WeightPrice
     */
    private $subject;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $sortProcessorMock = $this->createMock(SortProcessor::class);
        $sortProcessorMock->expects($this->any())->method('processAsc')->will($this->returnValueMap([
            [
                [
                    '_empty',
                    ['weight' => 50, 'price' => 50],
                    ['weight' => 20, 'price' => 20],
                    ['weight' => 70, 'price' => 70]
                ],
                [
                    '_empty',
                    ['weight' => 20, 'price' => 20],
                    ['weight' => 50, 'price' => 50],
                    ['weight' => 70, 'price' => 70]
                ]
            ]
        ]));

        $escaperMock = $this->createMock(Escaper::class);
        $escaperMock->expects($this->any())->method('escapeHtml')->willReturnArgument(0);

        $this->subject = $objectManager->getObject(WeightPrice::class, [
            'sortProcessor' => $sortProcessorMock,
            'escaper' => $escaperMock
        ]);
    }

    public function testGetWeightPrices()
    {
        $weightPrices = [
            'CA' => false,
            'FR' => ['weight_price' => false],
            'US' => [
                'weight_price' => [
                    '_empty',
                    ['weight' => 50, 'price' => 50],
                    ['weight' => 20, 'price' => 20],
                    ['weight' => 70, 'price' => 70]
                ]
            ]
        ];
        $this->subject->setValue($weightPrices);

        $expected = [
            new DataObject(['weight' => 20, 'price' => 20, '_countrypriceid' => 1, '_id' => 'US']),
            new DataObject(['weight' => 50, 'price' => 50, '_countrypriceid' => 2, '_id' => 'US']),
            new DataObject(['weight' => 70, 'price' => 70, '_countrypriceid' => 3, '_id' => 'US'])
        ];

        $this->assertEquals($expected, $this->subject->getWeightPrices());
    }

    public function testGetWeightPricesEmpty()
    {
        $this->assertEmpty($this->subject->getWeightPrices());
    }
}
