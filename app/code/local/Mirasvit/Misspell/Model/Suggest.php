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



/**
 * @category Mirasvit
 */
class Mirasvit_Misspell_Model_Suggest extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('misspell/suggest');
    }

    public function loadByQuery($query)
    {
        $this->setQuery($query);

        if (!$this->getId()) {
            $suggestText = Mage::getModel('misspell/misspell')->getSuggest($query);
            $this->setSuggest($suggestText);
            $this->save();
        }

        return $this;
    }
}
