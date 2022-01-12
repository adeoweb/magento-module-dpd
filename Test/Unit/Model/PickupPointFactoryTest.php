<?php

namespace AdeoWeb\Dpd\Test\Unit\Model;

use AdeoWeb\Dpd\Api\Data\PickupPointInterface;
use AdeoWeb\Dpd\Model\PickupPointFactory;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use PHPUnit\Framework\MockObject\MockObject;

class PickupPointFactoryTest extends AbstractTest
{
    /**
     * @var PickupPointFactory
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $typeResolverMock;

    /**
     * @var MockObject
     */
    private $objectManagerMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->typeResolverMock = $this->createMock(\AdeoWeb\Dpd\Model\PickupPoint\ResolverInterface::class);
        $this->objectManagerMock = $this->createMock(\Magento\Framework\ObjectManagerInterface::class);

        $this->subject = $this->objectManager->getObject(PickupPointFactory::class, [
            'typeResolver' => $this->typeResolverMock,
            'objectManager' => $this->objectManagerMock
        ]);
    }

    public function testCreateFromResponseWithArrayResponseData()
    {
        $pickupPointMock = $this->createMock(\AdeoWeb\Dpd\Api\Data\PickupPointInterface::class);

        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with('\\AdeoWeb\\Dpd\\Api\\Data\\PickupPointInterface', [])
            ->willReturn($pickupPointMock);

        $this->typeResolverMock->expects($this->once())
            ->method('resolve')
            ->with($pickupPointMock)
            ->willReturn('A');

        $result = $this->subject->createFromResponseData([
            'parcelshop_id' => 1,
            'company' => 'TestCompany',
            'country' => 'US',
            'city' => 'TestCity',
            'pcode' => '4848',
            'street' => 'TestStreet',
            'email' => 'test@email.com',
            'phone' => '+884848484',
            'longitude' => '51.1541515',
            'latitude' => '41.51848'
        ]);

        $this->assertInstanceOf(PickupPointInterface::class, $result);
    }
}
