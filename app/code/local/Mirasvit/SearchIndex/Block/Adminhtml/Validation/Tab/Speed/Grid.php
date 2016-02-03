<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Sphinx Search Ultimate
 * @version   2.3.2
 * @build     1290
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_SearchIndex_Block_Adminhtml_Validation_Tab_Speed_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('validation_grid');
        $this->setSaveParametersInSession(true);
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header' => $this->__('Product ID'),
            'index' => 'id',
            'sortable' => false,
            'filter' => false,
        ));

        $this->addColumn('relevance', array(
            'header' => $this->__('Relevance'),
            'index' => 'relevance',
            'sortable' => false,
            'filter' => false,
        ));

        return parent::_prepareColumns();
    }
}
