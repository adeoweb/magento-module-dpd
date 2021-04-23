<?php
namespace AdeoWeb\Dpd\Plugin\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\ShippingMethodManagement as ShippingMethodManagementBase;
use AdeoWeb\Dpd\Model\Provider\PickupPoint\AllowedWeightsInterface;

class ShippingMethodManagement {
    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var AllowedWeightsInterface
     */
    private $allowedWeight;

    public function __construct(CartRepositoryInterface $quoteRepository, AllowedWeightsInterface $allowedWeight)
    {
        $this->quoteRepository = $quoteRepository;
        $this->allowedWeight = $allowedWeight;
    }

    public function afterEstimateByExtendedAddress(ShippingMethodManagementBase $subject, $output, $cartId, $address)
    {
        return $this->filterOutput($output, $cartId, $address);
    }

    private function filterOutput($output, $cartId, $address)
    {
        try {
            $cart = $this->quoteRepository->get($cartId);
        } catch (NoSuchEntityException $e) {
            return $output;
        }

        $isValid = $this->allowedWeight->validate($cart, $address);

        if ($isValid) {
            return $output;
        }

        foreach ($output as $key => $shippingMethod) {
            if (
                $shippingMethod->getCarrierCode() == 'dpd'
                && $shippingMethod->getMethodCode() == 'pickup'
            ) {
                unset($output[$key]);
            }
        }

        return $output;
    }
}
