<?php

namespace AdeoWeb\Dpd\Model\Service;

interface ServiceInterface
{
    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function call(RequestInterface $request);
}
