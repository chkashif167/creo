<?php
class Mks_Bannerslider_Model_Mysql4_Imagegallery extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("bannerslider/imagegallery", "id");
    }
}