<?php

namespace AdeoWeb\Dpd\Model\Service\Dpd;

use Magento\Framework\ObjectManagerInterface;

class ResponseFactory
{
    const FIELD_STATUS = 'status';
    const FIELD_ERROR_MESSAGE = 'errlog';

    const RESPONSE_STATUS_OK = 'ok';
    const RESPONSE_STATUS_ERROR = 'err';

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * @param array $response
     * @return Response\Response
     */
    public function create(array $response)
    {
        if (empty($response)) {
            throw new \RuntimeException('Invalid response from DPD service');
        }

        $body = [];
        $status = null;
        $errorMessage = null;

        foreach ($response as $key => $value) {
            switch ($key) {
                case self::FIELD_STATUS:
                    $status = ($value !== self::RESPONSE_STATUS_OK);
                    break;
                case self::FIELD_ERROR_MESSAGE:
                    $errorMessage = $value;
                    break;
                default:
                    $body[$key] = $value;
                    break;
            }
        }

        return $this->objectManager->create(
            Response\Response::class,
            ['error' => $status, 'errorMessage' => $errorMessage, 'body' => $body]
        );
    }
}