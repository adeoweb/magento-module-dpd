<?php

declare(strict_types=1);

namespace AdeoWeb\Dpd\Model\Email;

use Magento\Framework\DataObject;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\ShipmentCommentCreationInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Container\ShipmentIdentity;
use Magento\Sales\Model\Order\Email\Container\Template;
use Magento\Sales\Model\Order\Email\Sender;
use Magento\Sales\Model\Order\Email\SenderBuilderFactory;
use Magento\Sales\Model\Order\Shipment\SenderInterface;
use Psr\Log\LoggerInterface;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Sales\Model\ResourceModel\Order\Shipment;
use Magento\Payment\Helper\Data;

class SendTrackingDataEmail extends Sender implements SenderInterface
{
    private const SHIPMENT_TEMPLATE = 'dpd_shipment_email_new';
    private const SHIPMENT_TEMPLATE_GUEST = 'dpd_shipment_email_new_guest';

    /**
     * @var Data
     */
    private $paymentHelper;

    /**
     * @var Shipment
     */
    private $shipmentResource;

    /**
     * @var ScopeConfigInterface
     */
    private $globalConfig;

    public function __construct(
        ScopeConfigInterface $globalConfig,
        Template $templateContainer,
        ShipmentIdentity $identityContainer,
        LoggerInterface $logger,
        SenderBuilderFactory $senderBuilderFactory,
        Renderer $addressRenderer,
        Shipment $shipmentResource,
        Data $paymentHelper
    ) {
        parent::__construct(
            $templateContainer,
            $identityContainer,
            $senderBuilderFactory,
            $logger,
            $addressRenderer
        );

        $this->globalConfig = $globalConfig;
        $this->shipmentResource = $shipmentResource;
        $this->paymentHelper = $paymentHelper;
    }

    public function send(
        OrderInterface $order,
        ShipmentInterface $shipment,
        ShipmentCommentCreationInterface $comment = null,
        $forceSyncMode = false
    ): bool {
        $shipment->setSendEmail($this->identityContainer->isEnabled());

        if (!$this->globalConfig->getValue('sales_email/general/async_sending') || $forceSyncMode) {
            $this->identityContainer->setStore($order->getStore());

            $tracksCollection = $shipment->getTracksCollection();

            $shipmentItems = $shipment->getAllItems();

            $transport = [
                'order' => $order,
                'order_id' => $order->getId(),
                'shipment' => $shipment,
                'tracks_collection' => $tracksCollection,
                'shipment_items' => $shipmentItems,
                'shipment_id' => $shipment->getId(),
                'comment' => $comment ? $comment->getComment() : '',
                'billing' => $order->getBillingAddress(),
                'payment_html' => $this->getPaymentHtml($order),
                'store' => $order->getStore(),
                'formattedShippingAddress' => $this->getFormattedShippingAddress($order),
                'formattedBillingAddress' => $this->getFormattedBillingAddress($order),
                'order_data' => [
                    'customer_name' => $order->getCustomerName(),
                    'is_not_virtual' => $order->getIsNotVirtual(),
                    'email_customer_note' => $order->getEmailCustomerNote(),
                    'frontend_status_label' => $order->getFrontendStatusLabel()
                ]
            ];
            $transportObject = new DataObject($transport);

            $this->templateContainer->setTemplateVars($transportObject->getData());

            if ($this->checkAndSend($order)) {
                $shipment->setEmailSent(true);

                $this->shipmentResource->saveAttribute($shipment, ['send_email', 'email_sent']);

                return true;
            }
        } else {
            $shipment->setEmailSent(null);

            $this->shipmentResource->saveAttribute($shipment, 'email_sent');
        }

        $this->shipmentResource->saveAttribute($shipment, 'send_email');

        return false;
    }

    protected function prepareTemplate(Order $order): void
    {
        $this->templateContainer->setTemplateOptions($this->getTemplateOptions());

        if ($order->getCustomerIsGuest()) {
            $templateId = self::SHIPMENT_TEMPLATE_GUEST;
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $templateId = self::SHIPMENT_TEMPLATE;
            $customerName = $order->getCustomerName();
        }

        $this->identityContainer->setCustomerName($customerName);
        $this->identityContainer->setCustomerEmail($order->getCustomerEmail());
        $this->templateContainer->setTemplateId($templateId);
    }

    private function getPaymentHtml(OrderInterface $order): string
    {
        return $this->paymentHelper->getInfoBlockHtml(
            $order->getPayment(),
            $this->identityContainer->getStore()->getStoreId()
        );
    }
}
