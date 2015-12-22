<?php
/**
 * MageWorx
 * Admin Order Grid  extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersGrid
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersGrid_Block_Adminhtml_Sales_Order_Grid_Renderer_Comments extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        /** @var MageWorx_OrdersGrid_Helper_Data $helper */
        $helper = Mage::helper('mageworx_ordersgrid');
        $comments = explode("\n", $this->htmlEscape($row->getData($this->getColumn()->getIndex())));
        $comments = array_reverse($comments);
        $limit = $helper->getNumberComments();
        if ($limit > 0) {
            array_splice($comments, $limit);
        }

        if (strpos(Mage::app()->getRequest()->getRequestString(), '/exportCsv/')) {
            return implode('|', $comments);
        }

        $count = count($comments);
        $prefix = 'c';

        if ($count > 3) {
            $comments[$count - 1] .= '<a href="" onclick="$(\'hdiv_' . $row->getData('increment_id') . '_' . $prefix . '\').style.display=\'none\'; $(\'a_' . $row->getData('increment_id') . '_' . $prefix . '\').style.display=\'block\'; return false;" style="float:right; font-weight:bold; text-decoration: none;" title="' . $helper->__('Less..') . '">↑</a>'
                . '</div>'
                . '<a href="" id="a_' . $row->getData('increment_id') . '_' . $prefix . '" onclick="$(\'hdiv_' . $row->getData('increment_id') . '_' . $prefix . '\').style.display=\'block\'; this.style.display=\'none\'; return false;" style="float:right; font-weight:bold; text-decoration: none;" title="' . $helper->__('More..') . '">↓</a>';
            $comments[2] .= '<div id="hdiv_' . $row->getData('increment_id') . '_' . $prefix . '" style="display:none">' . $comments[3];
            unset($comments[3]);
        }
        return '<div style="cursor: text">' . implode('<br/>', $comments) . '</div><a/>';
    }
}