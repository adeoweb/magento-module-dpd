<?php

namespace AdeoWeb\Dpd\Plugin\Block\Checkout;

use AdeoWeb\Dpd\Helper\Config;
use Magento\Checkout\Block\Checkout\LayoutProcessor as CheckoutLayoutProcessor;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Asset\Repository;
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

    /**
     * @var Repository
     */
    private $assetRepository;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Config $carrierConfig,
        Repository $assetRepository
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->carrierConfig = $carrierConfig;
        $this->assetRepository = $assetRepository;
    }

    public function afterProcess(
        CheckoutLayoutProcessor $subject,
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

                $componentSettings = [
                    'component' => $component,
                ];

                if ($methodCode === 'pickup') {
                    $componentSettings = $this->addPickupPointComponentContext($componentSettings);
                }

                $layoutConfiguration['dpd_method_' . $methodCode . '_component_' . $key] = $componentSettings;
            }
        }

        $previousConfig = $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
        ['children']['shippingAddress']['children']['shippingAdditional']['children'];

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shippingAdditional']['children'] = \array_merge(
            $previousConfig,
            $layoutConfiguration
        );

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

    /**
     * @param array $componentSettings
     * @return array
     */
    private function addPickupPointComponentContext(array $componentSettings)
    {
        $result = [
            'googleMapsEnabled' => $this->isPickupPointGoogleMapsEnabled(),
            'countryCenters' => $this->carrierConfig->getCode(Config::TYPE_PICKUP_POINT_MAP_COUNTRY_CENTERS),
            'activeIconImage' => $this->assetRepository->getUrl('AdeoWeb_Dpd::images/dpd-marker.png')
        ];

        return array_merge($componentSettings, $result);
    }

    private function isPickupPointGoogleMapsEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            'carriers/dpd/pickup/google_maps_enabled',
            ScopeInterface::SCOPE_WEBSITE
        );
    }
}
