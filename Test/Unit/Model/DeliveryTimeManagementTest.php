<?php

namespace AdeoWeb\Dpd\Test\Unit\Model;

use AdeoWeb\Dpd\Helper\Config;
use AdeoWeb\Dpd\Model\DeliveryTimeManagement;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;

class DeliveryTimeManagementTest extends AbstractTest
{
    /**
     * @var DeliveryTimeManagement
     */
    private $subject;

    public function setUp(): void
    {
        parent::setUp();

        $carrierConfig = $this->objectManager->getObject(Config::class);

        $this->subject = $this->objectManager->getObject(DeliveryTimeManagement::class, [
            'carrierConfig' => $carrierConfig,
        ]);
    }

    public function testCalculateException()
    {
        $carrierConfigMock = $this->createMock(Config::class);
        $carrierConfigMock->expects($this->any())
            ->method('getCode')
            ->withConsecutive(['classic_delivery_time_city', 'Kaunas'], ['classic_delivery_time', 'a'])
            ->willReturnOnConsecutiveCalls(['a'], false);

        $subject = $this->objectManager->getObject(DeliveryTimeManagement::class, [
            'carrierConfig' => $carrierConfigMock,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid configuration');

        return $subject->calculate('Kaunas');
    }

    public function testCalculate()
    {
        $tests =
            [
                [
                    'input' => 'Kaunas',
                    'expectedOutput' => [
                        0 =>
                            [
                                'value' => '2',
                                'label' => '8:00 - 18:00',
                            ],
                        1 =>
                            [
                                'value' => '1',
                                'label' => '8:00 - 14:00',
                            ],
                        2 =>
                            [
                                'value' => '5',
                                'label' => '14:00 - 18:00',
                            ],
                        3 =>
                            [
                                'value' => '7',
                                'label' => '18:00 - 22:00',
                            ],
                    ],
                ],
                [
                    'input' => 'kaunas',
                    'expectedOutput' => [
                        0 =>
                            [
                                'value' => '2',
                                'label' => '8:00 - 18:00',
                            ],
                        1 =>
                            [
                                'value' => '1',
                                'label' => '8:00 - 14:00',
                            ],
                        2 =>
                            [
                                'value' => '5',
                                'label' => '14:00 - 18:00',
                            ],
                        3 =>
                            [
                                'value' => '7',
                                'label' => '18:00 - 22:00',
                            ],
                    ],
                ],
                [
                    'input' => 'Siauliai',
                    'expectedOutput' => [
                        0 =>
                            [
                                'value' => '2',
                                'label' => '8:00 - 18:00',
                            ],
                        1 =>
                            [
                                'value' => '1',
                                'label' => '8:00 - 14:00',
                            ],
                        2 =>
                            [
                                'value' => '5',
                                'label' => '14:00 - 18:00',
                            ],
                        3 =>
                            [
                                'value' => '7',
                                'label' => '18:00 - 22:00',
                            ],
                    ],
                ],
                [
                    'input' => 'TestCity',
                    'expectedOutput' => [],
                ],
            ];

        foreach ($tests as $test) {
            $this->assertEquals($test['expectedOutput'], $this->subject->calculate($test['input']));
        }
    }
}
