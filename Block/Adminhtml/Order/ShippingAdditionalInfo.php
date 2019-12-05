<?php

namespace AdeoWeb\Dpd\Block\Adminhtml\Order;

use AdeoWeb\Dpd\Api\Data\Shipping\DeliveryOptionsInterface;
use AdeoWeb\Dpd\Api\PickupPointRepositoryInterface;
use AdeoWeb\Dpd\Helper\Config;
use AdeoWeb\Dpd\Helper\Config\Serializer;
use AdeoWeb\Dpd\Model\Carrier;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;

class ShippingAdditionalInfo extends Template
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var PickupPointRepositoryInterface
     */
    private $pickupPointRepository;

    /**
     * @var Config
     */
    private $carrierConfig;

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(
        Template\Context $context,
        Registry $registry,
        PickupPointRepositoryInterface $pickupPointRepository,
        Config $carrierConfig,
        Serializer $serializer,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->registry = $registry;
        $this->pickupPointRepository = $pickupPointRepository;
        $this->carrierConfig = $carrierConfig;
        $this->serializer = $serializer;
    }

    /**
     * @return OrderInterface
     * @throws \Exception
     */
    public function getOrder()
    {
        if ($order = $this->registry->registry('current_order')) {
            return $order;
        } elseif ($shipment = $this->registry->registry('current_shipment')) {
            return $shipment->getOrder();
        } elseif ($creditmemo = $this->registry->registry('current_creditmemo')) {
            return $creditmemo->getOrder();
        } elseif ($invoice = $this->registry->registry('current_invoice')) {
            return $invoice->getOrder();
        } else {
            throw new \Exception('Order is not set');
        }
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getDeliveryOptions()
    {
        return $this->serializer->unserialize($this->getOrder()->getData('dpd_delivery_options'));
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getAdditionalInfo()
    {
        $result = [];

        foreach ($this->getDeliveryOptions() as $optionKey => $value) {
            switch ($optionKey) {
                case DeliveryOptionsInterface::INDEX_API_ID:
                    $pickupPoint = $this->getPickupPoint($value);

                    if ($pickupPoint) {
                        $result[] = [
                            'label' => __('Pickup Point'),
                            'value' => '(' . $pickupPoint->getApiId() . ') ' . $pickupPoint->getCompany() . ', ' .
                                $pickupPoint->getStreet() . ', ' . $pickupPoint->getPostcode() . ', ' .
                                $pickupPoint->getCity()
                        ];
                    }
                    break;

                case DeliveryOptionsInterface::INDEX_DELIVERY_TIME:
                    $deliveryTimeValue = $this->carrierConfig->getCode(Config::TYPE_CLASSIC_DELIVERY_TIME, $value);

                    if ($deliveryTimeValue) {
                        $result[] = [
                            'label' => __('Delivery Time'),
                            'value' => $deliveryTimeValue
                        ];
                    }
                    break;
            }
        }

        return $result;
    }

    /**
     * @param string $id
     * @return \AdeoWeb\Dpd\Api\Data\PickupPointInterface|null
     */
    protected function getPickupPoint($id)
    {
        try {
            return $this->pickupPointRepository->getByApiId($id);
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function _toHtml()
    {
        /** @var Order $order */
        $order = $this->getOrder();

        if (strpos($order->getShippingMethod(), Carrier::CODE . '_') === false) {
            return '';
        }

        return parent::_toHtml();
    }
}
