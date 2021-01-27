<?php

namespace AdeoWeb\Dpd\Model\Carrier\Method;

use AdeoWeb\Dpd\Config\Restrictions;
use AdeoWeb\Dpd\Helper\Config\Serializer;
use AdeoWeb\Dpd\Model\Carrier\MethodInterface;
use AdeoWeb\Dpd\Helper\Utils;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Shipping\Helper\Carrier;
use Magento\Framework\App\RequestInterface;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use AdeoWeb\Dpd\Config\Api;

/**
 * Class Saturday
 * @codeCoverageIgnore
 */
class Sameday extends AbstractMethod implements MethodInterface
{
    const DPD_SERVICE = 'SD';
    const CODE = 'sameday';
    private const DPD_B2C = 'B2C';

    protected $code = self::CODE;

    /**
     * @var Utils
     */
    private $utils;

    /**
     * @var Api
     */
    private $apiConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        MethodFactory $rateMethodFactory,
        RequestInterface $request,
        Carrier $carrierHelper,
        Serializer $serializer,
        Utils $utils,
        Api $apiConfig,
        Restrictions $restrictionsConfig = null,
        array $validators = []
    ) {
        parent::__construct(
            $scopeConfig,
            $rateMethodFactory,
            $request,
            $carrierHelper,
            $serializer,
            $utils,
            $restrictionsConfig,
            $validators
        );

        $this->apiConfig = $apiConfig;
        $this->utils = $utils;
    }

    protected function getServiceType(): string
    {
        $result = parent::getServiceType();

        $apiUrl = $this->apiConfig->getUrl();

        if ($this->utils->getTldFromUrl($apiUrl) == 'lt') {
            $result .= self::DPD_B2C;
        }

        return $result;
    }
}
