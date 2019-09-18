<?php

namespace AdeoWeb\Dpd\Plugin\Block\Adminhtml\View;

class Form
{
    /**
     * @param \Magento\Shipping\Block\Adminhtml\View\Form $subject
     * @param $result
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterGetPrintLabelButton(\Magento\Shipping\Block\Adminhtml\View\Form $subject, $result)
    {
        $result .= $this->getCancelDpdParcelsButton($subject);

        return $result;
    }

    /**
     * @param \Magento\Shipping\Block\Adminhtml\View\Form $subject
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getCancelDpdParcelsButton(\Magento\Shipping\Block\Adminhtml\View\Form $subject)
    {
        $data['shipment_id'] = $subject->getShipment()->getId();

        $url = $subject->getUrl('dpd/action/cancelParcels', $data);

        return $subject->getLayout()->createBlock(
            \Magento\Backend\Block\Widget\Button::class
        )->setData(
            ['label' => __('Cancel DPD Parcels'), 'onclick' => 'setLocation(\'' . $url . '\')']
        )->toHtml();
    }
}
