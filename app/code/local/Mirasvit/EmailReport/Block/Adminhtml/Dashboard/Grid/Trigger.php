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
 * @package   Follow Up Email
 * @version   1.0.2
 * @build     564
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_EmailReport_Block_Adminhtml_Dashboard_Grid_Trigger extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    static $_triggers = array();

    public function render(Varien_Object $row)
    {
        $id = $row->getTriggerId();

        if (!isset(self::$_triggers[$id])) {
            $trigger = Mage::getModel('email/trigger')->load($id);

            if ($trigger->getId()) {
                self::$_triggers[$id] = $trigger->getTitle();
            } else {
                self::$_triggers[$id] = '';
            }
        }

        return self::$_triggers[$id];
    }
}
