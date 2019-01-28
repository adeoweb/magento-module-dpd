<?php

namespace AdeoWeb\Dpd\Controller\Adminhtml\Action;

use AdeoWeb\Dpd\Api\CancelParcelsManagementInterface;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;

class CancelParcels extends Action implements HttpGetActionInterface
{
    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @var CancelParcelsManagementInterface
     */
    private $cancelParcelsManagement;

    public function __construct(
        Action\Context $context,
        ShipmentRepositoryInterface $shipmentRepository,
        CancelParcelsManagementInterface $cancelParcelsManagement
    ) {
        parent::__construct($context);

        $this->shipmentRepository = $shipmentRepository;
        $this->cancelParcelsManagement = $cancelParcelsManagement;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     */
    public function execute()
    {
        $shipmentId = $this->getRequest()->getParam('shipment_id');

        if (!$shipmentId) {
            $this->messageManager->addErrorMessage(__('Shipment ID is not specified'));

            return $this->_redirect($this->_redirect->getRefererUrl());
        }

        try {
            $shipment = $this->getShipment($shipmentId);

            $this->cancelParcelsManagement->cancelParcels($shipment);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());

            return $this->_redirect($this->_redirect->getRefererUrl());
        }

        $this->messageManager->addSuccessMessage(__('DPD Parcels were successfully canceled'));

        return $this->_redirect($this->_redirect->getRefererUrl());
    }

    /**
     * @param $shipmentId
     * @return ShipmentInterface
     */
    private function getShipment($shipmentId)
    {
        return $this->shipmentRepository->get($shipmentId);
    }

    /**
     * @return bool
     * @codeCoverageIgnore
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('AdeoWeb_Dpd::dpd_cancel_parcels');
    }
}