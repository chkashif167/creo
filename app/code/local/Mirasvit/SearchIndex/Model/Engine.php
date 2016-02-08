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



abstract class Mirasvit_SearchIndex_Model_Engine
{
    abstract public function query($queryText, $store, $index);

    protected function _normalize($input)
    {
        if (!count($input)) {
            return $input;
        }

        $result = array();
        $max = max(array_values($input));
        $max = $max > 0 ? $max : 1;
        foreach ($input as $key => $value) {
            $result[$key] = intval($value / $max * 100);
        }

        return $result;
    }

    protected function _getReadAdapter()
    {
        return Mage::getSingleton('core/resource')->getConnection('core_read');
    }
}
