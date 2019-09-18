<?php

namespace AdeoWeb\Dpd\Model\Service\Dpd\Response;

use AdeoWeb\Dpd\Model\Service\ResponseInterface;

class Response implements ResponseInterface
{
    /**
     * @var boolean
     */
    private $error;

    /**
     * @var string
     */
    private $errorMessage;

    /**
     * @var array
     */
    private $body;

    public function __construct($error, $errorMessage, $body)
    {
        $this->error = $error;
        $this->errorMessage = $errorMessage;
        $this->body = $body;
    }

    /**
     * @return boolean
     * @codeCoverageIgnore
     */
    public function hasError()
    {
        return $this->error;
    }

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * @param null|string $index
     * @return array
     */
    public function getBody($index = null)
    {
        if (!$index) {
            return $this->body;
        }

        if (!\array_key_exists($index, $this->body)) {
            return [];
        }

        return $this->body[$index];
    }
}
