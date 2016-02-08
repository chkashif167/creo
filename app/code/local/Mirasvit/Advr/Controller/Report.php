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
 * @package   Advanced Reports
 * @version   1.0.1
 * @build     539
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



abstract class Mirasvit_Advr_Controller_Report extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->_setActiveMenu('advr');

        return $this;
    }

    protected function _processActions()
    {
        if ($this->getRequest()->getParam('export')) {
            $this->_exportAction();
        }

        if ($this->getRequest()->getParam('grid')) {
            $this->_gridAction();
        }

        return $this;
    }

    protected function _exportAction()
    {
        $type = $this->getRequest()->getParam('type');
        $grid = $this->_getGridBlock();

        if ($type == 'csv') {
            $fileName = 'report.csv';
            $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
        } elseif ($type == 'xml') {
            $fileName = 'report.xml';
            $this->_prepareDownloadResponse($fileName, $grid->getExcelFile());
        }
    }

    protected function _gridAction()
    {
        $grid = $this->_getGridBlock();

        $columns = $this->getRequest()->getParam('columns');

        foreach ($columns as $idx => $column) {
            if (isset($column['visible'])) {
                $columns[$idx]['hidden'] = false;
            } else {
                $columns[$idx]['hidden'] = true;
            }
        }

        try {
            $grid->saveConfiguration(new Varien_Object(array('columns' => $columns)));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        $this->getResponse()->setRedirect(Mage::helper('core/url')->getCurrentUrl());

        return $this;
    }

    protected function _getGridBlock()
    {
        return $this->getLayout()->getBlock('grid');
    }
}
