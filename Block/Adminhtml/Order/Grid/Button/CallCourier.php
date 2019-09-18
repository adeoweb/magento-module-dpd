<?php

namespace AdeoWeb\Dpd\Block\Adminhtml\Order\Grid\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class CallCourier implements ButtonProviderInterface
{
    /**
     * Retrieve button-specified settings
     *
     * @return array
     */
    public function getButtonData()
    {
        return [
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
    }
}
