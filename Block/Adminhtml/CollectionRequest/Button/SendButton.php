<?php

namespace AdeoWeb\Dpd\Block\Adminhtml\CollectionRequest\Button;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * @codeCoverageIgnore
 */
class SendButton extends AbstractBlock implements ButtonProviderInterface
{
    /**
     * Retrieve button-specified settings
     *
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Send'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
        ];
    }
}
