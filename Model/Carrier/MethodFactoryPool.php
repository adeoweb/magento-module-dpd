<?php

namespace AdeoWeb\Dpd\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;

class MethodFactoryPool
{
    private $methodFactories;

    /**
     * @param array $methodFactories
     */
    public function __construct(
        array $methodFactories = []
    ) {
        $this->methodFactories = $methodFactories;
    }

    /**
     * @param $methodCode
     * @return null|mixed
     */
    public function get($methodCode)
    {
        if (!isset($this->methodFactories[$methodCode])) {
            return null;
        }
        return $this->methodFactories[$methodCode];
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->methodFactories;
    }

    /**
     * @param $methodCode
     * @param RateRequest|null $request
     * @return null|MethodInterface
     */
    public function getInstance($methodCode, RateRequest $request = null)
    {
        $methodFactory = $this->get($methodCode);

        if (!$methodFactory) {
            return null;
        }

        /** @var MethodInterface $method */
        $method = $methodFactory->create();

        if ($request) {
            $method->setRequest($request);
        }

        return $method;

    }
}