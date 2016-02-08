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



class Mirasvit_Advr_Adminhtml_Advr_GeoController extends Mirasvit_Advr_Controller_Report
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_title($this->__('Advanced Reports'))
            ->_title($this->__('Geo Data'));

        parent::_initAction();

        return $this;
    }

    public function importAction()
    {
        $this->_initAction()
            ->_title($this->__('Import'));

        $this->_validate();

        $this->loadLayout();

        $this->_addContent($this->getLayout()->createBlock('advr/adminhtml_geo_import'));
        $this->renderLayout();
    }

    public function processImportAction()
    {
        if ($files = $this->getRequest()->getParam('files')) {
            foreach ($files as $file) {
                Mage::getSingleton('advr/postcode')->importFile($file);

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('advr')->__('File "%s" was successfully imported', $file)
                );
            }
        }

        $this->_redirect('*/*/import');
    }

    protected function _validate()
    {
        $path = Mage::getSingleton('advr/config')->getGeoFilesPath();

        if (!file_exists($path)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('advr')->__("Directory '$path' is not readable or does not exist.")
            );
        } else {
            $files = Mage::getSingleton('advr/system_config_source_geoImportFile')->toOptionArray();
            if (count($files) == 0) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('advr')->__("Directory '$path' is empty. Please upload data files.")
                );
            }
        }

        return $this;
    }

    protected function _isAllowed()
    {
        return (bool)Mage::getSingleton('admin/session')->isAllowed('advr/settings/import_geo')
            || Mage::getSingleton('admin/session')->isAllowed('report/advr/settings/import_geo');
    }
}
