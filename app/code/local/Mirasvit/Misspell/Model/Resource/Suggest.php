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
class Mirasvit_Misspell_Model_Resource_Suggest extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('misspell/misspell_suggest', 'suggest_id');
    }

    public function loadByQuery($object)
    {
        $read = $this->_getReadAdapter();

        $select = $this->_getLoadSelect('query', $object->getQuery(), $object);

        $data = $read->fetchRow($select);

        if ($data) {
            $object->setData($data);
        }

        return $this;
    }
}
