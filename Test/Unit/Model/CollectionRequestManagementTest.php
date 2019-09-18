<?php

namespace AdeoWeb\Dpd\Test\Unit\Model;

use AdeoWeb\Dpd\Api\Data\LocationInterface;
use AdeoWeb\Dpd\Api\LocationRepositoryInterface;
use AdeoWeb\Dpd\Helper\SubjectReader\CollectionRequestRequest;
use AdeoWeb\Dpd\Model\CollectionRequestManagement;
use AdeoWeb\Dpd\Model\Service\Dpd\Request\CollectionRequestImportRequest;
use AdeoWeb\Dpd\Model\Service\Dpd\Request\CollectionRequestImportRequestFactory;
use AdeoWeb\Dpd\Model\Service\ServiceInterface;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use Magento\Customer\Model\Address;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order;

class CollectionRequestManagementTest extends AbstractTest
{
    /**
     * @var CollectionRequestManagement
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $orderMock;

    /**
     * @var MockObject
     */
    private $shippingAddressMock;

    /**
     * @var MockObject
     */
    private $locationRepositoryMock;

    /**
     * @var MockObject
     */
    private $locationMock;

    /**
     * @var MockObject
     */
    private $collectionRequestImportRequestMock;

    /**
     * @var MockObject
     */
    private $carrierServiceMock;

    public function setUp()
    {
        parent::setUp();

        $this->orderMock = $this->createMock(Order::class);
        $this->locationMock = $this->createMock(LocationInterface::class);
        $this->shippingAddressMock = $this->createMock(Address::class);
        $this->collectionRequestImportRequestMock = $this->createMock(CollectionRequestImportRequest::class);
        $this->carrierServiceMock = $this->createMock(ServiceInterface::class);

        $collectionRequestRequestReader = $this->objectManager->getObject(CollectionRequestRequest::class);
        
        $orderRepositoryMock = $this->createMock(\Magento\Sales\Api\OrderRepositoryInterface::class);
        $orderRepositoryMock->expects($this->any())
            ->method('get')
            ->with(1)
            ->willReturn($this->orderMock);

        $this->locationRepositoryMock = $this->createMock(LocationRepositoryInterface::class);
        $this->locationRepositoryMock->expects($this->any())
            ->method('getById')
            ->with(1)
            ->willReturn($this->locationMock);
        
        $this->orderMock->expects($this->any())
            ->method('getShippingAddress')
            ->willReturn($this->shippingAddressMock);

        $this->locationMock->expects($this->any())
            ->method('getAddress')
            ->willReturn(['TestAddress1', 'TestAddress2']);

        $collectionRequestImportRequestFactoryMock = $this->createMock(CollectionRequestImportRequestFactory::class);
        $collectionRequestImportRequestFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->collectionRequestImportRequestMock);

        $this->subject = $this->objectManager->getObject(CollectionRequestManagement::class, [
            'collectionRequestRequestReader' => $collectionRequestRequestReader,
            'orderRepository' => $orderRepositoryMock,
            'locationRepository' => $this->locationRepositoryMock,
            'collectionRequestImportRequestFactory' => $collectionRequestImportRequestFactoryMock,
            'carrierService' => $this->carrierServiceMock
        ]);
    }

    public function testCollectionRequestWithResponseError()
    {
        $requestData = array(
            'request' => array(
                'order_id' => '1',
                'sender_adress' =>
                    array(
                        'sender_use_shipping_address' => '1',
                    ),
                'recipient_adress' =>
                    array(
                        'recipient_use_shipping_address' => '0',
                        'recipient_location' => '1',
                    ),
                'package_info' =>
                    array(
                        'comment' => 'TestComment',
                        'pickup_date' => '01/01/2000',
                        'num_of_parcels' => '1',
                        'total_weight' => '0.1',
                    ),
            )
        );

        $this->carrierServiceMock->expects($this->once())
            ->method('call')
            ->with($this->collectionRequestImportRequestMock)
            ->willReturn('error');

        $this->expectException(\Exception::class);

        return $this->subject->collectionRequest($requestData);
    }

    public function testCollectionRequestWithLocationException()
    {
        $requestData = array(
            'request' => array(
                'order_id' => '1',
                'sender_adress' =>
                    array(
                        'sender_use_shipping_address' => '1',
                    ),
                'recipient_adress' =>
                    array(
                        'recipient_use_shipping_address' => '0',
                        'recipient_location' => false,
                    ),
                'package_info' =>
                    array(
                        'comment' => 'TestComment',
                        'pickup_date' => '01/01/2000',
                        'num_of_parcels' => '1',
                        'total_weight' => '0.1',
                    ),
            )
        );

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Location is not specified');

        return $this->subject->collectionRequest($requestData);
    }

    public function testCollectionRequest()
    {
        $requestData = array(
            'request' => array(
                'order_id' => '1',
                'sender_adress' =>
                    array(
                        'sender_use_shipping_address' => '0',
                        'sender_location' => '1'
                    ),
                'recipient_adress' =>
                    array(
                        'recipient_use_shipping_address' => '1'
                    ),
                'package_info' =>
                    array(
                        'comment' => 'TestComment',
                        'pickup_date' => '01/01/2000',
                        'num_of_parcels' => '1',
                        'total_weight' => '0.1',
                    ),
            )
        );

        $this->carrierServiceMock->expects($this->once())
            ->method('call')
            ->with($this->collectionRequestImportRequestMock)
            ->willReturn('201 OK');

        $this->assertTrue($this->subject->collectionRequest($requestData));
    }
}
