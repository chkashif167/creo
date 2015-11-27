<?php

/**
 * Facebook Custom Audience Retargeting Pixel
 * 
 * @category    StrongTics
 * @package     FacebookConversionTracker
 * @author      Issa BERTHE <issaberthet@gmail.com>
 * @copyright   Copyright (c) StrongTics
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class StrongTics_FacebookConversionTracker_Block_Retargeting extends StrongTics_FacebookConversionTracker_Block_Abstract {

    protected function _isRetargetingEnabled() {
        return $this->_getConfigHelper()->isRetargetingEnabled();
    }

    public function getRetargetingPixelId() {
        return $this->_getConfigHelper()->getRetargetingPixelId();
    }

    protected function _canShow() {
        if (!$this->getRetargetingPixelId() || !$this->_isRetargetingEnabled()) {

            return false;
        }

        return true;
    }

}
