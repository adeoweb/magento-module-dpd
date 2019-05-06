<?php

namespace AdeoWeb\Dpd\Controller\Adminhtml\Collection\Request;

use AdeoWeb\Dpd\Api\CollectionRequestManagementInterface;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\RedirectFactory;

class Send extends Action
{
    const ADMIN_RESOURCE = 'AdeoWeb_Dpd::dpd_collection_request';

    /**
     * @var CollectionRequestManagementInterface
     */
    private $collectionRequestManagement;

    public function __construct(
        Action\Context $context,
        RedirectFactory $resultRedirectFactory,
        CollectionRequestManagementInterface $collectionRequestManagement
    ) {
        parent::__construct($context);
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->collectionRequestManagement = $collectionRequestManagement;
    }

    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->resultRedirectFactory->create()->setPath('*/*/index');
        }

        $data = $this->getRequest()->getParams();

        try {
            $this->collectionRequestManagement->collectionRequest($data);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());

            return $this->resultRedirectFactory->create()->setPath('*/*/index');
        }

        $this->messageManager->addSuccessMessage(__('DPD Collection Request successfully called'));

        return $this->resultRedirectFactory->create()->setPath('sales/order/index');
    }
}