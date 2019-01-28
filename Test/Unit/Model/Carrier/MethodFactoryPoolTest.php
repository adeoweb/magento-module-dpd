<?php

namespace AdeoWeb\Dpd\Test\Unit\Model\Carrier;

use AdeoWeb\Dpd\Model\Carrier\Method\ClassicFactory;
use AdeoWeb\Dpd\Model\Carrier\MethodFactoryPool;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use PHPUnit\Framework\MockObject\MockObject;

class MethodFactoryPoolTest extends AbstractTest
{
    /**
     * @var MethodFactoryPool
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $classicMethodFactoryMock;

    public function setUp()
    {
        parent::setUp();

        $this->classicMethodFactoryMock = $this->createMock(ClassicFactory::class);

        $this->subject = $this->objectManager->getObject(MethodFactoryPool::class, [
            'methodFactories' => [
                'classic' => $this->classicMethodFactoryMock,
            ],
        ]);
    }

    public function testGetWithNonExistingMethodCode()
    {
        $result = $this->subject->get('test');

        $this->assertNull($result);
    }

    public function testGet()
    {
        $result = $this->subject->get('classic');

        $this->assertInstanceOf(ClassicFactory::class, $result);
    }

    public function testGetInstanceWithNonExistingMethodCode()
    {
        $result = $this->subject->getInstance('test');

        $this->assertNull($result);
    }

    public function testGetInstance()
    {
        $classicMethodMock = $this->createMock(\AdeoWeb\Dpd\Model\Carrier\MethodInterface::class);

        $this->classicMethodFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($classicMethodMock);

        $rateRequestMock = $this->createMock(\Magento\Quote\Model\Quote\Address\RateRequest::class);

        $result = $this->subject->getInstance('classic', $rateRequestMock);

        $this->assertInstanceOf(\AdeoWeb\Dpd\Model\Carrier\MethodInterface::class, $result);
    }
}
