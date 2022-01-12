<?php

namespace AdeoWeb\Dpd\Test\Unit\Block\Adminhtml\Order\Grid\Button;

use AdeoWeb\Dpd\Block\Adminhtml\Order\Grid\Button\CallCourier;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;

class CallCourierTest extends AbstractTest
{
    /**
     * @var CallCourier
     */
    private $subject;

    public function setUp(): void
    {
        parent::setUp();

        $this->subject = $this->objectManager->getObject(CallCourier::class);
    }

    public function testGetButtonData()
    {
        $result = $this->subject->getButtonData();
        $expectedResult = [
            'label' => __('Call DPD Courier'),
            'class' => 'action-secondary',
            'data_attribute' => [
                'mage-init' => [
                    'Magento_Ui/js/form/button-adapter' => [
                        'actions' => [
                            [
                                'targetName' => 'sales_order_grid.sales_order_grid.dpd_call_courier_modal',
                                'actionName' => 'openModal'
                            ]
                        ]
                    ]
                ]
            ],
            'on_click' => ''
        ];

        $this->assertEquals($result, $expectedResult);
    }
}
