<?php

namespace AdeoWeb\Dpd\Plugin\Model\AdminOrder;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;

class Create
{
    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(
        RequestInterface $request
    ) {
        $this->request = $request;
    }

    /**
     * @param \Magento\Sales\Model\AdminOrder\Create $subject
     */
    public function beforeCreateOrder( $subject)
    {
        $deliveryOptions = $this->request->getParam('dpd_delivery_options');

        if (empty($deliveryOptions)) {
            return;
        }

        $deliveryOptions = new DataObject($deliveryOptions);

        $subject->getQuote()->setData('dpd_delivery_options', $deliveryOptions->toJson());
    }
}