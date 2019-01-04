<?php

namespace AdeoWeb\Dpd\Model\Service;

interface ResponseInterface
{
    /**
     * @return boolean
     */
    public function hasError();

    /**
     * @return string
     */
    public function getErrorMessage();

    /**
     * @param null|string $index
     * @return array
     */
    public function getBody($index = null);
}