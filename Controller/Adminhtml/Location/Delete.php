<?php

namespace AdeoWeb\Dpd\Controller\Adminhtml\Location;

use AdeoWeb\Dpd\Api\LocationRepositoryInterface;
use AdeoWeb\Dpd\Controller\Adminhtml\Location;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;

class Delete extends Location
{
    /**
     * @var LocationRepositoryInterface
     */
    private $locationRepository;

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        LocationRepositoryInterface $locationRepository
    ) {
        parent::__construct($context, $coreRegistry);

        $this->locationRepository = $locationRepository;
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $locationId = $this->getRequest()->getParam('location_id');

        if (!$locationId) {
            $this->messageManager->addErrorMessage(__('We can\'t find a location to delete.'));

            return $resultRedirect->setPath('*/*/');
        }
        try {
            $location = $this->locationRepository->getById($locationId);

            $this->locationRepository->delete($location);

            $this->messageManager->addSuccessMessage(__('You deleted the location.'));

            return $resultRedirect->setPath('*/*/');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());

            return $resultRedirect->setPath('*/*/edit', ['location_id' => $locationId]);
        }
    }

    /**
     * {@override}
     * @return bool
     * @codeCoverageIgnore
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('AdeoWeb_Dpd::dpd_location_delete');
    }
}
