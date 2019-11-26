<?php

namespace AdeoWeb\Dpd\Block\System\Config\Form\Field;

use AdeoWeb\Dpd\Block\Adminhtml\Form\Field\Country;
use AdeoWeb\Dpd\Model\Adminhtml\System\Config\WeightPriceFactory;
use AdeoWeb\Dpd\Model\Adminhtml\System\Config\WeightPrice as WeightPriceModel;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Data\Form\Element\Text;

/**
 * @method string getHtmlId()
 * @method Text getElement()
 */
class Restrictions extends AbstractFieldArray
{
    const COLUMN_COUNTRY = 'country';
    const COLUMN_WEIGHT_PRICE = 'weight_price';
    const FIELD_VALUE = 'value';

    /**
     * @var Country
     */
    protected $countryRenderer;

    /**
     * @var WeightPrice
     */
    protected $weightPriceRenderer;

    /**
     * @var WeightPriceFactory
     */
    protected $weightPriceFactory;

    public function __construct(Context $context, WeightPriceFactory $weightPriceFactory, array $data = [])
    {
        parent::__construct($context, $data);
        $this->weightPriceFactory = $weightPriceFactory;
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getWeightPriceJs()
    {
        return $this->getWeightPriceRenderer()->getJsHtml();
    }

    /**
     * @return WeightPrice
     * @throws LocalizedException
     */
    public function getWeightPriceRenderer()
    {
        if (!$this->weightPriceRenderer) {
            $this->weightPriceRenderer = $this->getLayout()->createBlock(
                WeightPrice::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->weightPriceRenderer->setIsRemoveNewLines(true);
            $this->weightPriceRenderer->setElement($this->getData('element'));
        }

        return $this->weightPriceRenderer;
    }

    /**
     * {@override}
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setTemplate('AdeoWeb_Dpd::system/config/field/restrictions.phtml');
    }

    /**
     * {@override}
     * @return string
     * @throws \Exception
     */
    protected function _toHtml()
    {
        $html = parent::_toHtml();

        $this->_isPreparedToRender = false;

        return $html;
    }

    /**
     * @return Country
     * @throws LocalizedException
     */
    protected function getCountryRenderer()
    {
        if (!$this->countryRenderer) {
            $this->countryRenderer = $this->getLayout()->createBlock(
                Country::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }

        return $this->countryRenderer;
    }

    /**
     * {@override}
     * @return void
     * @throws LocalizedException
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            self::COLUMN_COUNTRY,
            ['label' => __('Country'), 'renderer' => $this->getCountryRenderer(),]
        );
        $this->addColumn(
            self::COLUMN_WEIGHT_PRICE,
            ['label' => __(''), 'renderer' => $this->getWeightPriceRenderer(),]
        );

        $this->getWeightPriceRenderer()->setHtmlId(uniqid() . '_id');
        $this->getWeightPriceRenderer()->setValue($this->getWeightPriceValue());
        $this->getCountryRenderer()->setId(uniqid() . '<%- _id %>');

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * {@override}
     * {@inheritDoc}
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $country = $row->getData(self::COLUMN_COUNTRY);
        $options = [];
        if ($country) {
            $options['option_' . $this->getCountryRenderer()->calcOptionHash($country)] = 'selected="selected"';
        }
        $row->setData('option_extra_attrs', $options);
    }

    /**
     * @return WeightPriceModel
     */
    protected function getWeightPriceValue(): WeightPriceModel
    {
        $weightPrice = $this->weightPriceFactory->create();

        $currentValue = $this->getElement()->getData(self::FIELD_VALUE) ?: [];
        $weightPrice->setValue($currentValue);

        return $weightPrice;
    }
}
