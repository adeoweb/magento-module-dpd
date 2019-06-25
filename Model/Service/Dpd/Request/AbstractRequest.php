<?php

namespace AdeoWeb\Dpd\Model\Service\Dpd\Request;

use Magento\Framework\Exception\LocalizedException;

abstract class AbstractRequest
{
    const ENDPOINT = '';

    const METHOD = 'POST';

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @var array
     */
    protected $requiredParams = [];

    /**
     * @var array
     */
    private $data = [];

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getEndpoint()
    {
        return static::ENDPOINT;
    }

    /**
     * @param string $param
     * @param string $value
     * @param int $maxLength
     * @return $this
     */
    public function setData($param, $value, $maxLength = null)
    {
        if ($maxLength) {
            $value = \substr($value, 0, $maxLength);
        }

        $value = \str_replace('+', '%2B', $value);

        $this->data[$param] = $value;

        return $this;
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function getParams()
    {
        $result = [];

        foreach ($this->params as $param) {
            if (\array_search($param, $this->requiredParams) !== false && empty($this->data[$param])) {
                throw new LocalizedException(__('DPD Request is not valid. Required parameter "%1" is missing',
                    $param));
            }

            if (empty($this->data[$param])) {
                continue;
            }

            $result[$param] = $this->data[$param];
        }

        return $result;
    }

    /**
     * @return bool
     * @codeCoverageIgnore
     */
    public function isFile()
    {
        return false;
    }

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getMethod()
    {
        return static::METHOD;
    }
}