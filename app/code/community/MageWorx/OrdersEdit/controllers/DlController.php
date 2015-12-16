<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_DlController extends Mage_Core_Controller_Front_Action
{

    /**
     * @return Mage_Core_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('core/session');
    }

    /**
     * @return Mage_Core_Controller_Varien_Action
     * @throws Mage_Core_Exception
     */
    public function fileAction()
    {
        // ordersedit/dl/file/id/1/file.png
        $fileId = (int) $this->getRequest()->getParam('id');
        /** @var MageWorx_OrdersEdit_Model_Upload_Files $files */
        $files = Mage::getSingleton('mageworx_ordersedit/upload_files')->load($fileId);
        /** @var MageWorx_OrdersEdit_Helper_Data  */
        $helper = Mage::helper('mageworx_ordersedit');

        if ($files->getId()) {            
            $file = $helper->isUploadFile($fileId);
            if (empty($file)) {
                Mage::throwException($helper->__('Sorry, there was an error getting the file'));
                return $this->_redirectReferer();            
            } 
            try {
                $helper->processDownload($file, $files->getFileName());
                exit;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                return $this->_redirect('/');
            }
        } else {                        
            $this->_getSession()->addNotice($helper->__('Requested file not available now'));
            return $this->_redirectReferer();
        }
    }        
    
}
