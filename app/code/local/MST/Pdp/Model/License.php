<?php
/**
 * Magento Support Team.
 * @category   MST
 * @package    MST_Pdp
 * @version    2.0
 * @author     Magebay Developer Team <info@magebay.com>
 * @copyright  Copyright (c) 2009-2013 MAGEBAY.COM. (http://www.magebay.com)
 */
class MST_Pdp_Model_License extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('pdp/license');
    }
}