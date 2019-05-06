<?php

namespace AdeoWeb\Dpd\Controller\Adminhtml\Action;

use AdeoWeb\Dpd\Api\CollectionRequestManagementInterface;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;

class CollectionRequest extends \Magento\Backend\App\Action
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

    /**
     * {@inheritDoc}
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
            $this->collectionRequestManagement->collectionRequest($data);
        } catch (\Exception $e) {
            $result['error'] = true;
            $result['message'] = $e->getMessage();
        }

        if (!$result['error']) {
            $result['message'] = __('DPD Collection Request successfully called');
        }

        return $this->resultJsonFactory->create()->setData($result);
    }

    /**
     * @return bool
     * @codeCoverageIgnore
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('AdeoWeb_Dpd::dpd_collection_request');
    }
}