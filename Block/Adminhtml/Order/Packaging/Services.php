<?php

namespace AdeoWeb\Dpd\Block\Adminhtml\Order\Packaging;

use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;

/**
 * Class ReturnLabelField
 * @codeCoverageIgnore
 */
class Services extends Template
{
    const TEMPLATE = 'order/packaging/services.phtml';

    const XML_PATH_AUTO_INCLUDE_DPD_LABELS = 'carriers/dpd/auto_include_return_label';
    const XML_PATH_AUTO_DOCUMENT_SERVICE = 'carriers/dpd/auto_document_return';

    protected $_template = self::TEMPLATE;

    /**
     * @return bool
     */
    public function isAutoIncludeReturnLabels()
    {
        return $this->_scopeConfig->isSetFlag(
            self::XML_PATH_AUTO_INCLUDE_DPD_LABELS,
            ScopeInterface::SCOPE_WEBSITES
        );
    }

    /**
     * @return bool
     */
    public function isAutoDocumentReturn()
    {
        return $this->_scopeConfig->isSetFlag(
            self::XML_PATH_AUTO_DOCUMENT_SERVICE,
            ScopeInterface::SCOPE_WEBSITES
        );
    }
}
