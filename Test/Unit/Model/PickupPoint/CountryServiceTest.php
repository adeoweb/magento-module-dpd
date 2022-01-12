<?php

declare(strict_types=1);

namespace AdeoWeb\Dpd\Test\Unit\Model\PickupPoint;

use AdeoWeb\Dpd\Api\Data\PickupPointInterface;
use AdeoWeb\Dpd\Api\Data\PickupPointSearchResultsInterface;
use AdeoWeb\Dpd\Api\PickupPointRepositoryInterface;
use AdeoWeb\Dpd\Model\PickupPoint\CountryService;
use AdeoWeb\Dpd\Model\Service\Dpd\Request\PickupPointSearchRequest;
use AdeoWeb\Dpd\Model\Service\Dpd\Request\PickupPointSearchRequestFactory;
use AdeoWeb\Dpd\Model\Service\ResponseInterface;
use AdeoWeb\Dpd\Model\Service\ServiceInterface;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class CountryServiceTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|PickupPointRepositoryInterface
     */
    private $pickupPointRepositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ServiceInterface
     */
    private $serviceMock;

    /**
     * @var CountryService
     */
    private $subject;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $searchRequestMock = $this->createMock(PickupPointSearchRequest::class);
        $pickupPointSearchRequestFactoryMock = $this->createConfiguredMock(PickupPointSearchRequestFactory::class, [
            'create' => $searchRequestMock
        ]);

        $this->serviceMock = $this->createMock(ServiceInterface::class);

        $searchCriteriaMock = $this->createMock(SearchCriteria::class);
        $searchCriteriaBuilderMock = $this->createConfiguredMock(SearchCriteriaBuilder::class, [
            'create' => $searchCriteriaMock
        ]);
        $searchCriteriaBuilderMock->method('addFilter')->willReturnSelf();

        $this->pickupPointRepositoryMock = $this->createMock(PickupPointRepositoryInterface::class);

        $this->subject = $objectManager->getObject(CountryService::class, [
            'pickupPointSearchRequestFactory' => $pickupPointSearchRequestFactoryMock,
            'service' => $this->serviceMock,
            'searchCriteriaBuilder' => $searchCriteriaBuilderMock,
            'pickupPointRepository' => $this->pickupPointRepositoryMock
        ]);
    }

    /**
     * @throws LocalizedException
     */
    public function testGetPickupPoints()
    {
        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->expects($this->once())->method('getBody')->will($this->returnValueMap([
            ['parcelshops', [['parcelshop_id' => 1]]]
        ]));

        $this->serviceMock->expects($this->once())->method('call')->willReturn($responseMock);

        $expected = [['parcelshop_id' => 1]];

        $this->assertEquals($expected, $this->subject->getPickupPoints('country_code'));
    }

    public function testGetPickupPointsHasError()
    {
        $this->expectException(\Magento\Framework\Exception\LocalizedException::class);
        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->expects($this->once())->method('hasError')->willReturn(true);
        $responseMock->expects($this->once())->method('getErrorMessage')->willReturn('Connection error.');

        $this->serviceMock->expects($this->once())->method('call')->willReturn($responseMock);

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Connection error.');

        $this->subject->getPickupPoints('country_code');
    }

    public function testGetPickupPointsNotArray()
    {
        $this->expectException(LocalizedException::class);
        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->expects($this->once())->method('getBody')->will($this->returnValueMap([
            ['parcelshops', null]
        ]));

        $this->serviceMock->expects($this->once())->method('call')->willReturn($responseMock);

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Invalid parcel shops data.');

        $this->subject->getPickupPoints('country_code');
    }

    /**
     * @throws CouldNotSaveException
     */
    public function testDisablePickupPoints()
    {
        $pickupPointMock = $this->createConfiguredMock(PickupPointInterface::class, ['getApiId' => 1]);
        $pickupPoint2Mock = $this->createConfiguredMock(PickupPointInterface::class, ['getApiId' => 2]);
        $pickupPointSearchResultsMock = $this->createConfiguredMock(PickupPointSearchResultsInterface::class, [
            'getItems' => [$pickupPointMock, $pickupPoint2Mock]
        ]);

        $this->pickupPointRepositoryMock->expects($this->once())
            ->method('getList')
            ->willReturn($pickupPointSearchResultsMock);

        $this->subject->disablePickupPoints('country_code', [['parcelshop_id' => 1]]);

        $this->assertTrue(true);
    }
}
