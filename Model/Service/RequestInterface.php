<?php

namespace AdeoWeb\Dpd\Model\Service;

use Magento\Framework\Exception\LocalizedException;

interface RequestInterface
{
    /**
     * @return string
     */
    public function getEndpoint();

    /**
     * @return array
     * @throws LocalizedException
     */
    public function getParams();

    /**
     * @return boolean
     */
    public function isFile();

    /**
     * @return string
     */
    public function getMethod();
}