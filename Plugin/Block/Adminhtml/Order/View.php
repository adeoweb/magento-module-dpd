<?php

namespace AdeoWeb\Dpd\Plugin\Block\Adminhtml\Order;

use Magento\Framework\View\LayoutInterface;

class View
{
    const ACTION_NAME = 'openModal';
    const TARGET_NAME = 'sales_order_view_shipment_grid.sales_order_view_shipment_grid.dpd_collection_request_modal';

    /**
     * @param \Magento\Sales\Block\Adminhtml\Order\View $subject
     * @param LayoutInterface $layout
     * @return array
     */
    public function beforeSetLayout(
        \Magento\Sales\Block\Adminhtml\Order\View $subject,
        LayoutInterface $layout
    ) {
        $order = $subject->getOrder();

        $shippingMethodInfo = $order->getShippingMethod(true);

        if (!$shippingMethodInfo) {
            return [$layout];
        }

        if ($shippingMethodInfo->getData('carrier_code') === 'dpd') {
            $subject->addButton(
                'dpd_collection_request',
                [
                    'label' => __('DPD Collection Request'),
                    'class' => 'action-secondary',
                    'data_attribute' => [
                        'mage-init' => [
                            'Magento_Ui/js/form/button-adapter' => [
                                'actions' => [
                                    [
                                        'targetName' => self::TARGET_NAME,
                                        'actionName' => self::ACTION_NAME
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'on_click' => ''
                ]
            );
        }

        return [$layout];
    }
}
