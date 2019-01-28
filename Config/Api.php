<?php

namespace AdeoWeb\Dpd\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Api
 * @codeCoverageIgnore
 */
class Api
{
    const XML_PATH_CARRIER_API_USERNAME = 'carriers/dpd/api/username';
    const XML_PATH_CARRIER_API_PASSWORD = 'carriers/dpd/api/password';
    const XML_PATH_CARRIER_API_ID = 'carriers/dpd/api/id';
    const XML_PATH_CARRIER_API_URL = 'carriers/dpd/api/url';
    const XML_PATH_CARRIER_API_DEBUG = 'carriers/dpd/api/debug';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CARRIER_API_USERNAME,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CARRIER_API_PASSWORD,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CARRIER_API_ID,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CARRIER_API_URL,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @return boolean
     */
    public function isDebugMode()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_CARRIER_API_DEBUG,
            ScopeInterface::SCOPE_WEBSITE
        );
    }
}