<?php

declare(strict_types=1);

namespace AdeoWeb\Dpd\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use AdeoWeb\Dpd\Helper\Utils;
use Magento\Checkout\Model\ConfigProviderInterface;

class CompositeConfigProvider implements ConfigProviderInterface
{
    private const XML_PATH_CARRIER_API_URL = 'carriers/dpd/api/url';
    private const DEFAULT_LANG_CODE = 'lt';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Utils
     */
    private $utils;

    public function __construct(ScopeConfigInterface $scopeConfig, Utils $utils)
    {
        $this->scopeConfig = $scopeConfig;
        $this->utils = $utils;
    }

    public function getConfig(): array
    {
        $config = [];
        $config['apiLangCode'] = $this->getApiLanguageCode();

        return $config;
    }

    private function getApiLanguageCode(): string
    {
        $apiUrl = $this->scopeConfig->getValue(
            self::XML_PATH_CARRIER_API_URL,
            ScopeInterface::SCOPE_WEBSITE
        );

        if (empty($apiUrl)) {
            return self::DEFAULT_LANG_CODE;
        }

        return $this->utils->getTldFromUrl($apiUrl);
    }
}
