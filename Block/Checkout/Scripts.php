<?php

namespace AdeoWeb\Dpd\Block\Checkout;

use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;

class Scripts extends Template
{
    const XML_PATH_DPD_PICKUP_POINT_GOOGLE_MAPS_ENABLED = 'carriers/dpd/pickup/google_maps_enabled';
    const XML_PATH_DPD_PICKUP_POINT_GOOGLE_MAPS_API_KEY = 'carriers/dpd/pickup/google_maps_api_key';

    /**
     * @return bool
     */
    public function isPickupPointGoogleMapsEnabled()
    {
        $isSet = $this->_scopeConfig->isSetFlag(
            self::XML_PATH_DPD_PICKUP_POINT_GOOGLE_MAPS_API_KEY,
            ScopeInterface::SCOPE_WEBSITES
        );

        return $isSet && $this->getPickupPointGoogleMapsApiKey();
    }

    /**
     * @return string
     */
    public function getPickupPointGoogleMapsApiKey()
    {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_DPD_PICKUP_POINT_GOOGLE_MAPS_API_KEY,
            ScopeInterface::SCOPE_WEBSITES
        );
    }
}
