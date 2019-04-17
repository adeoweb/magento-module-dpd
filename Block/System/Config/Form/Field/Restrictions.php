<?php

namespace AdeoWeb\Dpd\Block\System\Config\Form\Field;

use AdeoWeb\Dpd\Block\Adminhtml\Form\Field\Country;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\Exception\LocalizedException;

class Restrictions extends AbstractFieldArray
{
    /**
     * @var Country
     */
    protected $countryRenderer;

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_addButtonLabel = __('Add');
    }

    /**
     * @return Country
     * @throws LocalizedException
     */
    protected function getCountryRenderer()
    {
        if (!$this->countryRenderer) {
            $this->countryRenderer = $this->getLayout()->createBlock(
                Country::class, '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }

        return $this->countryRenderer;
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'country', [
                'label' => __('Country'),
                'renderer' => $this->getCountryRenderer(),
            ]
        );
        $this->addColumn('price', array('label' => __('Price')));
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * {@inheritDoc}
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $country = $row->getData('country');
        $options = [];
        if ($country) {
            $options['option_' . $this->getCountryRenderer()->calcOptionHash($country)] = 'selected="selected"';
        }
        $row->setData('option_extra_attrs', $options);
    }

    /**
     * @param string $columnName
     * @return string
     * @throws \Exception
     */
    public function renderCellTemplate($columnName)
    {
        if ($columnName === 'price') {
            $this->_columns[$columnName]['class'] = 'input-text required-entry validate-number';
            $this->_columns[$columnName]['style'] = 'width:50px';
        }

        return parent::renderCellTemplate($columnName);
    }
}