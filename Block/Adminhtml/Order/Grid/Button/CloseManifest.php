<?php

namespace AdeoWeb\Dpd\Block\Adminhtml\Order\Grid\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class CloseManifest extends \Magento\Framework\View\Element\AbstractBlock implements ButtonProviderInterface
{
    const ACTION = 'dpd/action/closeManifest';

    /**
     * Retrieve button-specified settings
     *
     * @return array
     */
    public function getButtonData()
    {
        $message = __('Are you sure you want to close DPD manifest?');

        return [
            'label' => __('Close DPD Manifest'),
            'on_click' => "confirmSetLocation('{$message}', '{$this->getCloseManifestUrl()}')",
            'class' => 'action-secondary'
        ];
    }

    /**
     * @return string
     */
    private function getCloseManifestUrl()
    {
        return $this->getUrl(self::ACTION);
    }
}
