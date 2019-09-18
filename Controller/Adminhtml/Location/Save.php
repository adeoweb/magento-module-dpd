<?php

namespace AdeoWeb\Dpd\Controller\Adminhtml\Location;

use AdeoWeb\Dpd\Api\Data\LocationInterfaceFactory;
use AdeoWeb\Dpd\Api\LocationRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use AdeoWeb\Dpd\Model\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

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
        DataPersistorInterface $dataPersistor,
        LocationRepositoryInterface $locationRepository,
        LocationInterfaceFactory $locationFactory
    ) {
        parent::__construct($context);

        $this->dataPersistor = $dataPersistor;
        $this->locationRepository = $locationRepository;
        $this->locationFactory = $locationFactory;
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->getRequest()->getPostValue();

        if (!$data) {
            return $resultRedirect->setPath('*/*/');
        }

        $locationId = $this->getRequest()->getParam('location_id');

        try {
            if (!$locationId) {
                $location = $this->locationFactory->create(['data' => $data]);
            } else {
                $location = $this->locationRepository->getById($locationId);
                $location->setData($data);
            }

            $this->locationRepository->save($location);

            $this->messageManager->addSuccessMessage(__('You saved the location.'));
            $this->dataPersistor->clear('adeoweb_dpd_location');

            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', ['location_id' => $location->getLocationId()]);
            }
            return $resultRedirect->setPath('*/*/');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the location.'));
        }

        $this->dataPersistor->set('adeoweb_dpd_location', $data);
        return $resultRedirect->setPath('*/*/edit', ['location_id' => $this->getRequest()->getParam('location_id')]);
    }

    /**
     * {@override}
     * @return bool
     * @codeCoverageIgnore
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('AdeoWeb_Dpd::dpd_location_view');
    }
}
