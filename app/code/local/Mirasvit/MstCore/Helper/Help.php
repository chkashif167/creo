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
 * @package   Advanced Product Feeds
 * @version   1.1.4
 * @build     702
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_MstCore_Helper_Help extends Mage_Core_Helper_Abstract
{
    protected $_help = array();

    public function field($field)
    {
        $backtrace = debug_backtrace();
        $form = explode('_', $backtrace[1]['class']);
        $scope = array();
        for ($i = 4; $i < count($form); $i++) {
            $scope[] = $form[$i];
        }

        $scope = strtolower(implode('_', $scope));

        $text = $this->getText($scope, $field);

        $text = htmlspecialchars($text);

        if ($text) {
            return '<button title="'.$text.'" onclick="return false;" class="mstcore-help-button back">?</button>';
        }
    }

    public function getText($scope, $code)
    {
        if (isset($this->_help[$scope]) && isset($this->_help[$scope][$code])) {
            $text = $this->_help[$scope][$code];
            $text = nl2br($text);
            return $text;
        }

        return $scope.'['.$code.']';
    }
}