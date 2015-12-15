<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Model_Resource_Upload_Files extends Mage_Core_Model_Mysql4_Abstract {
    
    protected function _construct() 
    {        
        $this->_init('mageworx_ordersedit/upload_files', 'entity_id');
    }
}