<?php

namespace AdeoWeb\Dpd\Test\Unit\Model\Carrier\Validator;

use AdeoWeb\Dpd\Model\Carrier\Validator\Timeframe;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use PHPUnit\Framework\MockObject\MockObject;

class TimeframeTest extends AbstractTest
{
    /**
     * @var Timeframe
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $scopeConfigMock;

    /**
     * @var MockObject
     */
    private $timezoneMock;

    public function setUp()
    {
        parent::setUp();

        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->timezoneMock = $this->createMock(\Magento\Framework\Stdlib\DateTime\TimezoneInterface::class);

        $this->subject = $this->objectManager->getObject(Timeframe::class, [
            'scopeConfig' => $this->scopeConfigMock,
            'timezone' => $this->timezoneMock
        ]);
    }

    public function testValidateWithInvalidData()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid validator data.');

        return $this->subject->validate([]);
    }

    public function testValidateWithNotAvailableTime()
    {
        $requestMock = $this->createPartialMock(DataObject::class, []);

        $this->timezoneMock->expects($this->once())
            ->method('getConfigTimezone')
            ->willReturn('Europe/London');

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with()
            ->willReturn('');

        $result = $this->subject->validate([
            'method_code' => 'classic',
            'request' => $requestMock
        ]);

        $this->assertFalse($result);
    }

    public function testValidate()
    {
        $requestMock = $this->createPartialMock(DataObject::class, []);

        $this->timezoneMock->expects($this->once())
            ->method('getConfigTimezone')
            ->willReturn('Europe/London');

        $config = $this->objectManager->getObject(\AdeoWeb\Dpd\Helper\Config::class);
        $allTimes = $config->getCode('available_times');

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with()
            ->willReturn(implode(',', array_keys($allTimes)));

        $result = $this->subject->validate([
            'method_code' => 'classic',
            'request' => $requestMock
        ]);

        $this->assertTrue($result);
    }
}
