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


class Mirasvit_Email_Block_Adminhtml_Event_Grid_Renderer_Args extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $args = unserialize($row->getData('args_serialized'));

        return $this->arrayToTable($args);
    }

    public function arrayToTable($args)
    {
        ksort($args);

        $html = '<table style="border: 0px;">';
        foreach ($args as $key => $value) {
            $html .= '<tr>';
            $html .= '<th width="100px;">'.$key.'</th>';
            $html .= '<td>'.$value.'</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';

        return $html;
    }
}