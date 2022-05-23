<?php

namespace AdeoWeb\Dpd\Block\Adminhtml\Order\Create\Shipping\Method\ShippingAdditional;

use AdeoWeb\Dpd\Helper\Config;
use AdeoWeb\Dpd\Model\Carrier;
use AdeoWeb\Dpd\Model\Carrier\Method\Classic;
use Magento\Framework\View\Element\Template;
use Magento\Backend\Model\Session\Quote;

class DeliveryTime extends Template
{
    /**
     * @var Quote
     */
    private $quoteSession;

    /**
     * @var Config
     */
    private $carrierConfig;

    /**
     * @param Template\Context $context
     * @param Quote $quoteSession
     * @param Config $carrierConfig
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Quote $quoteSession,
        Config $carrierConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->quoteSession = $quoteSession;
        $this->carrierConfig = $carrierConfig;
    }

    /**
     * Public method
     *
     * @return \Magento\Framework\Phrase
     */
    public function getInputLabel()
    {
        return __('Select DPD Delivery Time:');
    }

    /**
     * Public method
     *
     * @return string
     */
    public function getInputName()
    {
        return 'dpd_delivery_options[delivery_time]';
    }

    /**
     * Public method
     *
     * @return string
     */
    public function getInputId()
    {
        return 'dpd_delivery_time';
    }

    /**
     * Public method
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return $this->quoteSession->getQuote();
    }

    /**
     * Public method
     *
     * @return array
     */
    public function getOptions()
    {
        $result = [];

        $quoteCity = $this->getQuote()->getShippingAddress()->getCity();

        $deliveryTimeValue = $this->carrierConfig->getCode(Config::TYPE_CLASSIC_DELIVERY_TIME_CITY, $quoteCity);

        if (!$deliveryTimeValue) {
            return [];
        }

        foreach ($deliveryTimeValue as $item) {
            $result[] = [
                'label' => $this->carrierConfig->getCode(Config::TYPE_CLASSIC_DELIVERY_TIME, $item),
                'value' => $item
            ];
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function _toHtml()
    {
        $quote = $this->getQuote();

        $applicableShippingMethod = Carrier::CODE . '_' . Classic::CODE;

        if ($quote->getShippingAddress()->getShippingMethod() !== $applicableShippingMethod ||
            empty($this->getOptions())
        ) {
            return '';
        }

        return parent::_toHtml();
    }
}
