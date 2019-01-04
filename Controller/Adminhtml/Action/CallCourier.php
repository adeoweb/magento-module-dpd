<?php

namespace AdeoWeb\Dpd\Controller\Adminhtml\Action;

use AdeoWeb\Dpd\Api\CallCourierManagementInterface;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

class CallCourier extends Action implements HttpPostActionInterface
{
    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var CallCourierManagementInterface
     */
    private $callCourierManagement;

    public function __construct(
        Action\Context $context,
        JsonFactory $resultJsonFactory,
        CallCourierManagementInterface $callCourierManagement
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->callCourierManagement = $callCourierManagement;
    }

    /**
     * @return ResultInterface|ResponseInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            throw new LocalizedException(__('Only AJAX calls are permitted'));
        }

        $result = ['error' => false];

        $data = $this->getRequest()->getParams();

        try {
            $this->callCourierManagement->callCourier($data);
        } catch (\Exception $e) {
            $result['error'] = true;
            $result['message'] = $e->getMessage();
        }

        if (!$result['error']) {
            $result['message'] = __('DPD Courier called succesfully.');
        }

        return $this->resultJsonFactory->create()->setData($result);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('AdeoWeb_Dpd::dpd_call_courier');
    }
}