<?php

namespace AdeoWeb\Dpd\Controller\Adminhtml\Location;

use AdeoWeb\Dpd\Api\Data\LocationInterface;
use AdeoWeb\Dpd\Api\Data\LocationInterfaceFactory;
use AdeoWeb\Dpd\Api\LocationRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\Redirect;

class Edit extends \AdeoWeb\Dpd\Controller\Adminhtml\Location
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var LocationRepositoryInterface
     */
    private $locationRepository;

    /**
     * @var LocationInterfaceFactory
     */
    private $locationFactory;

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        LocationRepositoryInterface $locationRepository,
        LocationInterfaceFactory $locationFactory
    ) {
        parent::__construct($context, $coreRegistry);

        $this->resultPageFactory = $resultPageFactory;
        $this->locationRepository = $locationRepository;
        $this->locationFactory = $locationFactory;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $locationId = $this->getRequest()->getParam('location_id');

        try {
            if (!$locationId) {
                $location = $this->locationFactory->create();
            } else {
                $location = $this->locationRepository->getById($locationId);
            }

            $this->coreRegistry->register('adeoweb_dpd_location', $location);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());

            return $resultRedirect->setPath('*/*/index');
        }
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        $this->initPage($resultPage)->addBreadcrumb(
            $locationId ? __('Edit Location') : __('New Location'),
            $locationId ? __('Edit Location') : __('New Location')
        );

        $resultPage->getConfig()->getTitle()->prepend(__('Locations'));
        $resultPage->getConfig()->getTitle()->prepend(
            $location->getLocationId() ? __('Edit Location %1', $location->getLocationId()) :
                __('New Location')
        );

        return $resultPage;
    }

    /**
     * @return bool
     * @codeCoverageIgnore
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('AdeoWeb_Dpd::dpd_location_update');
    }
}
