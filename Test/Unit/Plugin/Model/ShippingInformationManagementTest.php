<?php

namespace AdeoWeb\Dpd\Test\Unit\Plugin\Model;

use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use AdeoWeb\Dpd\Plugin\Model\ShippingInformationManagement;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;

class ShippingInformationManagementTest extends AbstractTest
{
    /**
     * @var ShippingInformationManagement
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $subjectMock;

    /**
     * @var MockObject
     */
    private $addressInformationMock;

    /**
     * @var MockObject
     */
    private $quoteRepositoryMock;

    /**
     * @var MockObject
     */
    private $loggerMock;

    public function setUp()
    {
        parent::setUp();

        $this->subjectMock = $this->createMock(\Magento\Checkout\Model\ShippingInformationManagement::class);
        $this->addressInformationMock = $this->createMock(\Magento\Checkout\Api\Data\ShippingInformationInterface::class);

        $this->quoteRepositoryMock = $this->createMock(\Magento\Quote\Api\CartRepositoryInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->subject = $this->objectManager->getObject(ShippingInformationManagement::class, [
            'quoteRepository' => $this->quoteRepositoryMock,
            'logger' => $this->loggerMock
        ]);
    }

    public function testBeforeSaveAddressInformationWithNonApplicableCarrier()
    {
        $expectedAddressInformation = clone $this->addressInformationMock;

        $this->addressInformationMock->expects($this->once())
            ->method('getShippingCarrierCode')
            ->willReturn('test');

        $result = $this->subject->beforeSaveAddressInformation($this->subjectMock, 1, $this->addressInformationMock);
        $expectedResult = [1, $expectedAddressInformation];

        $this->assertEquals($expectedResult, $result);
    }

    public function testBeforeSaveAddressInformationWithQuoteException()
    {
        $expectedAddressInformation = clone $this->addressInformationMock;

        $this->addressInformationMock->expects($this->once())
            ->method('getShippingCarrierCode')
            ->willReturn('dpd');

        $this->quoteRepositoryMock->expects($this->once())
            ->method('getActive')
            ->with(1)
            ->willThrowException(new NoSuchEntityException(__('No such entity')));

        $result = $this->subject->beforeSaveAddressInformation($this->subjectMock, 1, $this->addressInformationMock);
        $expectedResult = [1, $expectedAddressInformation];

        $this->assertEquals($expectedResult, $result);
    }

    public function testBeforeSaveAddressInformationWithoutDeliveryOptions()
    {
        $expectedAddressInformation = clone $this->addressInformationMock;

        $this->addressInformationMock->expects($this->once())
            ->method('getShippingCarrierCode')
            ->willReturn('dpd');

        $quoteMock = $this->createMock(\Magento\Quote\Api\Data\CartInterface::class);

        $this->quoteRepositoryMock->expects($this->once())
            ->method('getActive')
            ->with(1)
            ->willReturn($quoteMock);

        $extensionAttributesSubstitute = $this->objectManager->getObject(DataObject::class);
        $extensionAttributesSubstitute->setDpdDeliveryOptions(null);

        $this->addressInformationMock->expects($this->once())
            ->method('getExtensionAttributes')
            ->willReturn($extensionAttributesSubstitute);

        $result = $this->subject->beforeSaveAddressInformation($this->subjectMock, 1, $this->addressInformationMock);
        $expectedResult = [1, $expectedAddressInformation];

        $this->assertEquals($expectedResult, $result);
    }

    public function testBeforeSaveAddressInformationWithSameDpdDeliveryOptions()
    {
        $expectedAddressInformation = clone $this->addressInformationMock;

        $this->addressInformationMock->expects($this->once())
            ->method('getShippingCarrierCode')
            ->willReturn('dpd');

        $quoteMock = $this->createPartialMock(Quote::class, []);

        $this->quoteRepositoryMock->expects($this->once())
            ->method('getActive')
            ->with(1)
            ->willReturn($quoteMock);

        $extensionAttributesSubstitute = $this->objectManager->getObject(DataObject::class);
        $extensionAttributesSubstitute->setDpdDeliveryOptions(new DataObject(['test' => 'test']));

        $this->addressInformationMock->expects($this->once())
            ->method('getExtensionAttributes')
            ->willReturn($extensionAttributesSubstitute);

        $quoteMock->setData('dpd_delivery_options', '{"test":"test"}');

        $result = $this->subject->beforeSaveAddressInformation($this->subjectMock, 1, $this->addressInformationMock);
        $expectedResult = [1, $expectedAddressInformation];

        $this->assertEquals($expectedResult, $result);
    }

    public function testBeforeSaveAddressInformationWithQuoteSaveException()
    {
        $expectedAddressInformation = clone $this->addressInformationMock;

        $this->addressInformationMock->expects($this->once())
            ->method('getShippingCarrierCode')
            ->willReturn('dpd');

        $quoteMock = $this->createPartialMock(Quote::class, []);

        $this->quoteRepositoryMock->expects($this->once())
            ->method('getActive')
            ->with(1)
            ->willReturn($quoteMock);

        $extensionAttributesSubstitute = $this->objectManager->getObject(DataObject::class);
        $extensionAttributesSubstitute->setDpdDeliveryOptions(new DataObject(['test' => 'test']));

        $this->addressInformationMock->expects($this->once())
            ->method('getExtensionAttributes')
            ->willReturn($extensionAttributesSubstitute);

        $quoteException = new \Exception();

        $this->quoteRepositoryMock->expects($this->once())
            ->method('save')
            ->willThrowException($quoteException);

        $this->loggerMock->expects($this->once())
            ->method('critical')
            ->with($quoteException);

        $result = $this->subject->beforeSaveAddressInformation($this->subjectMock, 1, $this->addressInformationMock);
        $expectedResult = [1, $expectedAddressInformation];

        $this->assertEquals($expectedResult, $result);
    }

    public function testBeforeSaveAddressInformation()
    {
        $expectedAddressInformation = clone $this->addressInformationMock;

        $this->addressInformationMock->expects($this->once())
            ->method('getShippingCarrierCode')
            ->willReturn('dpd');

        $quoteMock = $this->createPartialMock(Quote::class, []);

        $this->quoteRepositoryMock->expects($this->once())
            ->method('getActive')
            ->with(1)
            ->willReturn($quoteMock);

        $extensionAttributesSubstitute = $this->objectManager->getObject(DataObject::class);
        $extensionAttributesSubstitute->setDpdDeliveryOptions(new DataObject(['test' => 'test']));

        $this->addressInformationMock->expects($this->once())
            ->method('getExtensionAttributes')
            ->willReturn($extensionAttributesSubstitute);

        $result = $this->subject->beforeSaveAddressInformation($this->subjectMock, 1, $this->addressInformationMock);
        $expectedResult = [1, $expectedAddressInformation];

        $this->assertEquals($quoteMock->getData('dpd_delivery_options'), '{"test":"test"}');

        $this->assertEquals($expectedResult, $result);
    }
}
