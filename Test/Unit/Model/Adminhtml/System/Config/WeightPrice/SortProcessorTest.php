<?php

declare(strict_types=1);

namespace AdeoWeb\Dpd\Test\Unit\Model\Adminhtml\System\Config\WeightPrice;

use AdeoWeb\Dpd\Model\Adminhtml\System\Config\WeightPrice\SortProcessor;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class SortProcessorTest extends TestCase
{
    /**
     * @var SortProcessor
     */
    private $subject;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->subject = $objectManager->getObject(SortProcessor::class);
    }

    public function testProcessAsc()
    {
        $expected = [0 => 'empty', 2 => ['weight' => 5], 1 => ['weight' => 10], 3 => ['weight' => 18]];
        $array = ['empty', ['weight' => 10], ['weight' => 5], ['weight' => 18]];

        $this->assertEquals($expected, $this->subject->processAsc($array));
    }

    public function testProcessDesc()
    {
        $expected = [0 => 'empty', 3 => ['weight' => 18], 1 => ['weight' => 10], 2 => ['weight' => 5]];
        $array = ['empty', ['weight' => 10], ['weight' => 5], ['weight' => 18]];

        $this->assertEquals($expected, $this->subject->processDesc($array));
    }
}
