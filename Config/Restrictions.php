<?php

namespace AdeoWeb\Dpd\Config;

use AdeoWeb\Dpd\Block\System\Config\Form\Field\Restrictions as RestrictionsBlock;
use AdeoWeb\Dpd\Block\System\Config\Form\Field\WeightPrice;
use AdeoWeb\Dpd\Helper\Config\Serializer;
use AdeoWeb\Dpd\Model\Adminhtml\System\Config\WeightPrice\SortProcessor;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Restrictions
{
    const XML_PATH_METHOD_CLASSIC_RESTRICTIONS = 'carriers/dpd/%s/restrictions';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var string
     */
    private $method;

    /**
     * @var SortProcessor
     */
    private $sortProcessor;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Serializer $serializer,
        SortProcessor $sortProcessor,
        $method = 'classic'
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
        $this->method = $method;
        $this->sortProcessor = $sortProcessor;
    }

    /**
     * @param string|null $selectedCountry
     * @param float $currentWeight
     * @return null|int|string
     */
    public function getByCountryWeight($selectedCountry, $currentWeight)
    {
        $countryWeightPrices = $this->getCountryWeightPrices($selectedCountry);
        if (!$countryWeightPrices) {
            return null;
        }

        return $this->getWeightPrice($countryWeightPrices, $currentWeight);
    }

    /**
     * @param string|null $selectedCountry
     * @return array|null
     */
    private function getCountryWeightPrices($selectedCountry)
    {
        $countryPrices = $this->getConfigValue();
        if (!is_array($countryPrices)) {
            return null;
        }

        foreach ($countryPrices as $countryData) {
            $country = $countryData[RestrictionsBlock::COLUMN_COUNTRY] ?? null;
            if ($country && $country === $selectedCountry) {
                return $countryData;
            }
        }

        return null;
    }

    /**
     * @param array $countryWeightPrices
     * @param float $currentWeight
     * @return bool|int|mixed|null
     */
    private function getWeightPrice($countryWeightPrices, $currentWeight)
    {
        $weightPrices = $countryWeightPrices[RestrictionsBlock::COLUMN_WEIGHT_PRICE] ?? null;
        if (!$weightPrices) {
            return null;
        }

        $currentPrice = null;
        $sortedWeightPrices = $this->sortProcessor->processAsc($weightPrices);
        foreach ($sortedWeightPrices as $weightPrice) {
            if (!is_array($weightPrice)) {
                continue;
            }

            $configWeight = $weightPrice[WeightPrice::COLUMN_WEIGHT] ?? 0;
            if ($currentWeight < $configWeight) {
                return $currentPrice;
            }

            $currentPrice = $weightPrice[WeightPrice::COLUMN_PRICE] ?? $currentPrice;
        }

        return $currentPrice;
    }

    /**
     * @return array
     */
    private function getConfigValue()
    {
        $configValue = $this->scopeConfig->getValue(
            \sprintf(self::XML_PATH_METHOD_CLASSIC_RESTRICTIONS, $this->method),
            ScopeInterface::SCOPE_WEBSITE
        );

        $result = $this->serializer->unserialize($configValue);

        return \is_array($result) ? \array_values($result) : $result;
    }
}
