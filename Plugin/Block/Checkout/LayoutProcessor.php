<?php

namespace AdeoWeb\Dpd\Plugin\Block\Checkout;

use AdeoWeb\Dpd\Helper\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class LayoutProcessor
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Config
     */
    private $carrierConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Config $carrierConfig
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->carrierConfig = $carrierConfig;
    }

    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        $result
    ) {
        $jsLayout = $this->appendDpdMethodComponents($result);

        return $jsLayout;
    }

    /**
     * @param array $jsLayout
     * @return array|null
     */
    private function appendDpdMethodComponents(array $jsLayout)
    {
        if (!isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shippingAdditional']['children'])) {
            return null;
        }

        $layoutConfiguration = [];

        foreach ($this->getAllowedMethods() as $methodCode) {
            $components = $this->carrierConfig->getCode('method_js_components', $methodCode);

            if (!\is_array($components)) {
                continue;
            }

            foreach ($components as $key => $component) {
                if ($key === 'delivery-time' && !$this->isDeliveryTimeComponentEnabled()) {
                    continue;
                }

                $layoutConfiguration['dpd_method_' . $methodCode . '_component_' . $key] = [
                    'component' => $component
                ];
            }
        }

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shippingAdditional']['children'] = $layoutConfiguration;

        return $jsLayout;
    }

    /**
     * @return array
     */
    private function getAllowedMethods()
    {
        return \explode(',', $this->scopeConfig->getValue(
            'carriers/dpd/allowed_methods'
        ));
    }

    /**
     * @return bool
     */
    private function isDeliveryTimeComponentEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            'carriers/dpd/classic/delivery_times_enable',
            ScopeInterface::SCOPE_WEBSITE
        );
    }
}