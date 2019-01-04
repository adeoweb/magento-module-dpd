<?php

namespace AdeoWeb\Dpd\Controller\Adminhtml\Action;

use AdeoWeb\Dpd\Api\CollectionRequestManagementInterface;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;

class CollectionRequest extends \Magento\Backend\App\Action implements HttpPostActionInterface
{
    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var CollectionRequestManagementInterface
     */
    private $collectionRequestManagement;

    public function __construct(
        Action\Context $context,
        JsonFactory $resultJsonFactory,
        CollectionRequestManagementInterface $collectionRequestManagement
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->collectionRequestManagement = $collectionRequestManagement;
    }

    public function execute()
    {
        $result = ['error' => false];

        if (!$this->getRequest()->isXmlHttpRequest()) {
            $result['error'] = true;
        }

        $data = $this->getRequest()->getParams();

        try {
            $this->collectionRequestManagement->collectionRequest($data);
        } catch (\Exception $e) {
            $result['error'] = true;
            $result['message'] = $e->getMessage();
        }

        if (!$result['error']) {
            $result['message'] = __('Collection Request called succesfully.');
        }

        return $this->resultJsonFactory->create()->setData($result);
    }

    /**
     * Check Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('AdeoWeb_Dpd::dpd_collection_request');
    }
}