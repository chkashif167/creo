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



class Mirasvit_Advr_Block_Adminhtml_Block_Grid_Renderer_Action extends
Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    public function render(Varien_Object $row)
    {
        $actions = $this->getColumn()->getActions();
        if (empty($actions) || !is_array($actions)) {
            return '&nbsp;';
        }

        $html = array();

        foreach ($actions as $action) {
            if (is_array($action)) {
                if ($link = $this->_toLinkHtml($action, $row)) {
                    $html[] = $link;
                }
            }
        }

        return implode('&nbsp;|&nbsp;', $html);
    }

    protected function _toLinkHtml($action, $row)
    {
        $url = call_user_func($action['callback'], $row);

        if ($url) {
            return '<a href="' . $url . '">' . $action['caption'] . '</a>';
        }
    }
}
