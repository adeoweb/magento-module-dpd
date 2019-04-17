<?php

namespace AdeoWeb\Dpd\Model\Config\Source;

use Magento\Directory\Model\ResourceModel\Country\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Option\ArrayInterface;

class EuropeanCountries implements ArrayInterface
{
    const XML_PATH_EU_COUNTRIES = 'general/country/eu_countries';

    protected $options;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var CollectionFactory
     */
    private $countryCollectionFactory;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        CollectionFactory $countryCollectionFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->countryCollectionFactory = $countryCollectionFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function toOptionArray($isMultiselect = false, $foregroundCountries = '')
    {
        if (!$this->options) {
            $result = \array_map(function ($item) {
                return ['value' => $item->getCountryId(), 'label' => $item->getName()];
            }, $this->getEuCountries());

            $this->options = $result;
        }

        $options = $this->options;
        if (!$isMultiselect) {
            \array_unshift($options, ['value' => '', 'label' => __('--Please Select--')]);
        }

        return $options;
    }

    /**
     * @return array
     */
    public function getEuCountries()
    {
        $configValue = $this->scopeConfig->getValue(self::XML_PATH_EU_COUNTRIES);
        $configValue = \array_filter(\explode(',', $configValue));

        if (empty($configValue)) {
            return [];
        }

        $countryCollection = $this->countryCollectionFactory->create();
        $countryCollection->addCountryCodeFilter($configValue);

        return $countryCollection->getItems();
    }
}