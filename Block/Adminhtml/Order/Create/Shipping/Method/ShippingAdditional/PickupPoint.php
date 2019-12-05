<?php

namespace AdeoWeb\Dpd\Block\Adminhtml\Order\Create\Shipping\Method\ShippingAdditional;

use AdeoWeb\Dpd\Api\Data\Shipping\DeliveryOptionsInterface;
use AdeoWeb\Dpd\Model\Carrier;
use AdeoWeb\Dpd\Model\Carrier\Method\Pickup;
use AdeoWeb\Dpd\Model\PickupPointManagement;
use Magento\Framework\View\Element\Template;
use Magento\Backend\Model\Session\Quote;

class PickupPoint extends Template
{
    /**
     * @var Quote
     */
    private $quoteSession;

    /**
     * @var PickupPointManagement
     */
    private $pickupPointManagement;

    public function __construct(
        Template\Context $context,
        Quote $quoteSession,
        PickupPointManagement $pickupPointManagement,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->quoteSession = $quoteSession;
        $this->pickupPointManagement = $pickupPointManagement;
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getInputLabel()
    {
        return __('Select DPD Pickup Point:');
    }

    /**
     * @return string
     */
    public function getInputName()
    {
        return 'dpd_delivery_options[api_id]';
    }

    /**
     * @return string
     */
    public function getInputId()
    {
        return 'dpd_api_id';
    }

    /**
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return $this->quoteSession->getQuote();
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        $result = [];

        $pickupPointList = $this->pickupPointManagement->getList(
            $this->getQuote()->getShippingAddress()->getCountryId()
        );

        foreach ($pickupPointList as $pickupPoint) {
            $result[] = [
                'label' => $pickupPoint['company'],
                'value' => $pickupPoint[DeliveryOptionsInterface::INDEX_API_ID] ?? ''
            ];
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function _toHtml()
    {
        $quote = $this->getQuote();

        $applicableShippingMethod = Carrier::CODE . '_' . Pickup::CODE;

        if ($quote->getShippingAddress()->getShippingMethod() !== $applicableShippingMethod) {
            return '';
        }

        return parent::_toHtml();
    }
}
