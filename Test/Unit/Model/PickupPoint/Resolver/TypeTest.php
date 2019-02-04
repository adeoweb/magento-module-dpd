<?php

namespace AdeoWeb\Dpd\Test\Unit\Model\PickupPoint\Resolver;

use AdeoWeb\Dpd\Api\Data\PickupPointInterface;
use AdeoWeb\Dpd\Model\PickupPoint\Resolver\Type;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;

class TypeTest extends AbstractTest
{
    /**
     * @var Type
     */
    private $subject;

    public function setUp()
    {
        parent::setUp();

        $this->subject = $this->objectManager->getObject(Type::class);
    }

    public function testResolveWithException()
    {
        $pickupPointMock1 = $this->createConfiguredMock(PickupPointInterface::class, [
            'getApiId' => null,
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid pickup point provided. Parcelshop ID is missing');

        return $this->subject->resolve($pickupPointMock1);
    }

    public function testResolveWithLockers()
    {
        $pickupPointMock1 = $this->createConfiguredMock(PickupPointInterface::class, [
            'getApiId' => 'EE90001',
        ]);
        $pickupPointMock2 = $this->createConfiguredMock(PickupPointInterface::class, [
            'getApiId' => 'LV90001',
        ]);
        $pickupPointMock3 = $this->createConfiguredMock(PickupPointInterface::class, [
            'getApiId' => 'LT90001',
        ]);
        $pickupPointMock4 = $this->createConfiguredMock(PickupPointInterface::class, [
            'getApiId' => 'TEST90001',
        ]);

        $this->assertEquals(1, $this->subject->resolve($pickupPointMock1));
        $this->assertEquals(1, $this->subject->resolve($pickupPointMock2));
        $this->assertEquals(1, $this->subject->resolve($pickupPointMock3));
        $this->assertEquals(null, $this->subject->resolve($pickupPointMock4));
    }

    public function testResolveWithParcelshops()
    {
        $pickupPointMock1 = $this->createConfiguredMock(PickupPointInterface::class, [
            'getApiId' => 'EE10001',
        ]);
        $pickupPointMock2 = $this->createConfiguredMock(PickupPointInterface::class, [
            'getApiId' => 'LV10001',
        ]);
        $pickupPointMock3 = $this->createConfiguredMock(PickupPointInterface::class, [
            'getApiId' => 'LT10001',
        ]);
        $pickupPointMock4 = $this->createConfiguredMock(PickupPointInterface::class, [
            'getApiId' => 'TEST10001',
        ]);

        $this->assertEquals(2, $this->subject->resolve($pickupPointMock1));
        $this->assertEquals(2, $this->subject->resolve($pickupPointMock2));
        $this->assertEquals(2, $this->subject->resolve($pickupPointMock3));
        $this->assertEquals(null, $this->subject->resolve($pickupPointMock4));
    }

    public function testResolveWithRobot()
    {
        $pickupPointMock1 = $this->createConfiguredMock(PickupPointInterface::class, [
            'getApiId' => 'EE91001',
        ]);
        $pickupPointMock2 = $this->createConfiguredMock(PickupPointInterface::class, [
            'getApiId' => 'TEST10001',
        ]);


        $this->assertEquals(3, $this->subject->resolve($pickupPointMock1));
        $this->assertEquals(null, $this->subject->resolve($pickupPointMock2));
    }
}
