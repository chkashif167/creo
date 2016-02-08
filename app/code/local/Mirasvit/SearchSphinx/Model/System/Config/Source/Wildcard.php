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



class Mirasvit_SearchSphinx_Model_System_Config_Source_Wildcard
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Mirasvit_SearchSphinx_Model_Config::WILDCARD_INFIX,
                'label' => 'Enabled (*word*)',
            ),
            array(
                'value' => Mirasvit_SearchSphinx_Model_Config::WILDCARD_SUFFIX,
                'label' => 'Enabled at end (word*)',
            ),
            array(
                'value' => Mirasvit_SearchSphinx_Model_Config::WILDCARD_PREFIX,
                'label' => 'Enabled at start (*word)',
            ),
            array(
                'value' => Mirasvit_SearchSphinx_Model_Config::WILDCARD_DISABLED,
                'label' => 'Disabled',
            ),
        );
    }
}
