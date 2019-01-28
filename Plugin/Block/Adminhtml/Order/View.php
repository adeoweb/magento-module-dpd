<?php

namespace AdeoWeb\Dpd\Plugin\Block\Adminhtml\Order;

use Magento\Framework\View\LayoutInterface;

class View
{
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
                                        'targetName' => 'sales_order_view_shipment_grid.sales_order_view_shipment_grid.dpd_collection_request_modal',
                                        'actionName' => 'openModal'
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