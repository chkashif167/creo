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
 * @package   Advanced Reports
 * @version   1.0.1
 * @build     539
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_Advd_Block_Adminhtml_Widget_Abstract_Adminhtml_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function getRowUrl($item)
    {
        $res = parent::getRowUrl($item);
        if ((!$res || $res == '#') && $this->getRowUrlCallback()) {
            $res = call_user_func($this->getRowUrlCallback(), $item);
        }

        return ($res ? $res : '#');
    }
}
