<?php

namespace AdeoWeb\Dpd\Test\Unit\Model\Config\Source;

use AdeoWeb\Dpd\Helper\Config;
use AdeoWeb\Dpd\Model\Config\Source\Method;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use PHPUnit\Framework\MockObject\MockObject;

class MethodTest extends AbstractTest
{
    /**
     * @var Method
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $carrierConfigMock;

    public function setUp()
    {
        parent::setUp();

        $this->carrierConfigMock = $this->createMock(Config::class);

        $this->subject = $this->objectManager->getObject(Method::class, [
            'carrierConfig' => $this->carrierConfigMock
        ]);
    }

    public function testToOptionArray()
    {
        $this->carrierConfigMock->expects($this->once())
            ->method('getCode')
            ->with('method')
            ->willReturn([
                'classic' => __('Classic')
            ]);

        $result = $this->subject->toOptionArray();
        $expectedResult = [['value' => 'classic', 'label' => __('Classic')]];

        $this->assertEquals($expectedResult, $result);
    }
}
