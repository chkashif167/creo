<?php

    class Biztech_Trackorder_Helper_Data extends Mage_Core_Helper_Abstract
    {
        public function getTrackorderUrl()
        {
            return $this->_getUrl('trackorder/index');
        }
}