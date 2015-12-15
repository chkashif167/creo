<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Model_Upload_Files extends Mage_Core_Model_Abstract {

    protected function _construct() {
        parent::_construct();
        $this->_init('mageworx_ordersedit/upload_files');
    }

    public function removeFile()
    {        
        $fileId = $this->getEntityId();
        if ($fileId>0) {            
            $filePath = Mage::helper('mageworx_ordersedit')->getUploadFilesPath($fileId, false);
            @unlink($filePath);
        }        
    }
}