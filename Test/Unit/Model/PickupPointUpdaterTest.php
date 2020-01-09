<?php

declare(strict_types=1);

namespace AdeoWeb\Dpd\Test\Unit\Model;

use AdeoWeb\Dpd\Api\Data\PickupPointInterface;
use AdeoWeb\Dpd\Api\PickupPointRepositoryInterface;
use AdeoWeb\Dpd\Model\PickupPoint;
use AdeoWeb\Dpd\Model\PickupPoint\CountryService;
use AdeoWeb\Dpd\Model\PickupPointFactory;
use AdeoWeb\Dpd\Model\PickupPointUpdater;
use AdeoWeb\Dpd\Model\Provider\PickupPoint\AllowedCountries;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class PickupPointUpdaterTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|PickupPointRepositoryInterface
     */
    private $pickupPointRepositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|AllowedCountries
     */
    private $pickupAllowedCountriesProviderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|CountryService
     */
    private $countryServiceMock;

    /**
     * @var PickupPointUpdater
     */
    private $subject;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->pickupAllowedCountriesProviderMock = $this->createMock(AllowedCountries::class);
        $this->countryServiceMock = $this->createMock(CountryService::class);

        $pickupPointMock = $this->createMock(PickupPoint::class);

        $pickupPointFactoryMock = $this->createMock(PickupPointFactory::class);
        $pickupPointFactoryMock->method('createFromResponseData')->will($this->returnValueMap([
            [['api_id' => 1], [], new DataObject(['api_id' => 1])],
            [['api_id' => 2], [], new DataObject(['api_id' => 2])]
        ]));
        $pickupPointFactoryMock->method('create')->will($this->returnValueMap([
            [['api_id' => 2], $pickupPointMock]
        ]));

        $this->pickupPointRepositoryMock = $this->createMock(PickupPointRepositoryInterface::class);

        $this->subject = $objectManager->getObject(PickupPointUpdater::class, [
            'pickupAllowedCountriesProvider' => $this->pickupAllowedCountriesProviderMock,
            'countryService' => $this->countryServiceMock,
            'pickupPointFactory' => $pickupPointFactoryMock,
            'pickupPointRepository' => $this->pickupPointRepositoryMock
        ]);
    }

    /**
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function testExecute()
    {
        $this->pickupAllowedCountriesProviderMock->expects($this->once())
            ->method('get')
            ->willReturn(['country_code']);

        $pickupPointMock = $this->createConfiguredMock(PickupPoint::class, ['getApiId' => 1]);

        $this->pickupPointRepositoryMock->method('getByApiId')->will($this->returnValueMap([[1, $pickupPointMock]]));

        $this->countryServiceMock->method('getPickupPoints')->will($this->returnValueMap([
            ['country_code', [['api_id' => 1]]]
        ]));

        $this->assertTrue($this->subject->execute());
    }

    /**
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function testExecuteNewPickupPoint()
    {
        $this->pickupAllowedCountriesProviderMock->expects($this->once())
            ->method('get')
            ->willReturn(['country_code']);

        $this->pickupPointRepositoryMock->method('getByApiId')
            ->willThrowException(new NoSuchEntityException(__('No pickup point')));

        $this->countryServiceMock->method('getPickupPoints')->will($this->returnValueMap([
            ['country_code', [['api_id' => 2]]]
        ]));

        $this->assertTrue($this->subject->execute());
    }

    /**
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function testErrorMessage()
    {
        $this->pickupAllowedCountriesProviderMock->expects($this->once())
            ->method('get')
            ->willReturn(['country_code']);

        $this->countryServiceMock->method('getPickupPoints')->willThrowException(new LocalizedException(__('error')));

        $expected = ['country_code' => 'error'];

        $this->assertEquals($expected, $this->subject->execute());
    }
}
