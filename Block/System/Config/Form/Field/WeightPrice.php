<?php

declare(strict_types=1);

namespace AdeoWeb\Dpd\Block\System\Config\Form\Field;

use AdeoWeb\Dpd\Model\Adminhtml\System\Config\WeightPrice as WeightPriceModel;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Data\Form\Element\Text;

/**
 * @codeCoverageIgnore
 *
 * @method bool getIsRemoveNewLines()
 * @method string getHtmlId()
 * @method string getName()
 * @method WeightPriceModel getValue()
 *
 * @method $this setElement(Text $element)
 * @method $this setIsRemoveNewLines(bool $value)
 * @method $this setHtmlId(string $value)
 * @method $this setName(string $name)
 * @method $this setValue(string | array $value)
 */
class WeightPrice extends AbstractFieldArray
{
    const COLUMN_WEIGHT = 'weight';
    const COLUMN_PRICE = 'price';

    /**
     * @return $this
     */
    public function getElement()
    {
        return $this;
    }

    /**
     * {@override}
     * @return array
     */
    public function getArrayRows()
    {
        return $this->getValue()->getWeightPrices();
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getJsHtml()
    {
        /** @var self $block */
        $block = $this->getLayout()->createBlock(self::class, 'dpd.weight_price.js.' . uniqid());
        $block->setHtmlId($this->getHtmlId());
        $block->setInputName($this->getName());
        $block->setValue($this->getValue());
        $block->setTemplate('AdeoWeb_Dpd::system/config/field/weight_price_js.phtml');

        return $block->toHtml();
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * {@override}
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setTemplate('AdeoWeb_Dpd::system/config/field/weight_price.phtml');
    }

    /**
     * {@override}
     * @return string
     * @throws \Exception
     */
    protected function _toHtml()
    {
        return $this->getIsRemoveNewLines() ? str_replace(["\n", "\r"], '', parent::_toHtml()) : parent::_toHtml();
    }

    /**
     * {@override}
     */
    protected function _prepareToRender()
    {
        parent::_prepareToRender();

        $this->addColumn(self::COLUMN_WEIGHT, [
            'label' => __('Weight'),
            'type' => 'text',
            'class' => 'validate-zero-or-greater validate-number input-text require required-entry'
        ]);

        $this->addColumn(self::COLUMN_PRICE, [
            'label' => __('Price'),
            'type' => 'text',
            'class' => 'validate-zero-or-greater validate-number input-text require required-entry'
        ]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * {@override}
     * @param string $columnName
     * @return string
     * @throws \Exception
     */
    public function renderCellTemplate($columnName)
    {
        if (empty($this->_columns[$columnName])) {
            throw new \Exception('Wrong column name specified.');
        }

        $column = $this->_columns[$columnName];
        $inputName = $this->getElement()->getName()
            . '[<%- ' . WeightPriceModel::FIELD_COUNTRY_PRICE_ID . ' %>][' . $columnName . ']';

        if ($column['renderer']) {
            return $column['renderer']->setInputName($inputName)->setColumnName($columnName)->setColumn($column)
                ->toHtml();
        }

        return '<input type="text" name="' . $inputName . '" value="<%- ' . $columnName . ' %>" ' .
            ($column['size'] ? 'size="' . $column['size'] . '"' : '') . ' class="' .
            (isset($column['class']) ? $column['class'] : 'input-text') . '"' .
            (isset($column['style']) ? ' style="' . $column['style'] . '"' : '') . '/>';
    }
}
