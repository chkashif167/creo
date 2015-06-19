<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Page
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Page data helper
 */
class Mage_Page_Helper_Data extends Mage_Core_Helper_Abstract
{

}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Page
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Page layout helper
 *
 * @category   Mage
 * @package    Mage_Page
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Page_Helper_Layout extends Mage_Core_Helper_Abstract
{
    /**
     * Apply page layout handle
     *
     * @param string $pageLayout
     * @return Mage_Page_Helper_Layout
     */
    public function applyHandle($pageLayout)
    {
        $pageLayout = $this->_getConfig()->getPageLayout($pageLayout);

        if (!$pageLayout) {
            return $this;
        }

        $this->getLayout()
            ->getUpdate()
            ->addHandle($pageLayout->getLayoutHandle());

        return $this;
    }

    /**
     * Apply page layout template
     * (for old design packages)
     *
     * @param string $pageLayout
     * @return Mage_Page_Helper_Layout
     */
    public function applyTemplate($pageLayout = null)
    {
        if ($pageLayout === null) {
            $pageLayout = $this->getCurrentPageLayout();
        } else {
            $pageLayout = $this->_getConfig()->getPageLayout($pageLayout);
        }

        if (!$pageLayout) {
            return $this;
        }

        if ($this->getLayout()->getBlock('root') &&
            !$this->getLayout()->getBlock('root')->getIsHandle()) {
                // If not applied handle
                $this->getLayout()
                    ->getBlock('root')
                    ->setTemplate($pageLayout->getTemplate());
        }

        return $this;
    }

    /**
     * Retrieve current applied page layout
     *
     * @return Varien_Object|boolean
     */
    public function getCurrentPageLayout()
    {
        if ($this->getLayout()->getBlock('root') &&
            $this->getLayout()->getBlock('root')->getLayoutCode()) {
            return $this->_getConfig()->getPageLayout($this->getLayout()->getBlock('root')->getLayoutCode());
        }

        // All loaded handles
        $handles = $this->getLayout()->getUpdate()->getHandles();
        // Handles used in page layouts
        $pageLayoutHandles = $this->_getConfig()->getPageLayoutHandles();
        // Applied page layout handles
        $appliedHandles = array_intersect($handles, $pageLayoutHandles);

        if (empty($appliedHandles)) {
            return false;
        }

        $currentHandle = array_pop($appliedHandles);

        $layoutCode = array_search($currentHandle, $pageLayoutHandles, true);

        return $this->_getConfig()->getPageLayout($layoutCode);
    }

    /**
     * Retrieve page config
     *
     * @return Mage_Page_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('page/config');
    }
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Page
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Page layout config model
 *
 * @category   Mage
 * @package    Mage_Page
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Page_Model_Config
{
    const XML_PATH_PAGE_LAYOUTS = 'global/page/layouts';
    const XML_PATH_CMS_LAYOUTS = 'global/cms/layouts';

    /**
     * Available page layouts
     *
     * @var array
     */
    protected $_pageLayouts = null;

    /**
     * Initialize page layouts list
     *
     * @return Mage_Page_Model_Config
     */
    protected function _initPageLayouts()
    {
        if ($this->_pageLayouts === null) {
            $this->_pageLayouts = array();
            $this->_appendPageLayouts(self::XML_PATH_CMS_LAYOUTS);
            $this->_appendPageLayouts(self::XML_PATH_PAGE_LAYOUTS);
        }
        return $this;
    }

    /**
     * Fill in $_pageLayouts by reading layouts from config
     *
     * @param string $xmlPath XML path to layouts root
     * @return Mage_Page_Model_Config
     */
    protected function _appendPageLayouts($xmlPath)
    {
        if (!Mage::getConfig()->getNode($xmlPath)) {
            return $this;
        }
        if (!is_array($this->_pageLayouts)) {
            $this->_pageLayouts = array();
        }
        foreach (Mage::getConfig()->getNode($xmlPath)->children() as $layoutCode => $layoutConfig) {
            $this->_pageLayouts[$layoutCode] = new Varien_Object(array(
                'label'         => Mage::helper('page')->__((string)$layoutConfig->label),
                'code'          => $layoutCode,
                'template'      => (string)$layoutConfig->template,
                'layout_handle' => (string)$layoutConfig->layout_handle,
                'is_default'    => (int)$layoutConfig->is_default,
            ));
        }
        return $this;
    }

    /**
     * Retrieve available page layouts
     *
     * @return array
     */
    public function getPageLayouts()
    {
        $this->_initPageLayouts();
        return $this->_pageLayouts;
    }

    /**
     * Retrieve page layout by code
     *
     * @param string $layoutCode
     * @return Varien_Object|boolean
     */
    public function getPageLayout($layoutCode)
    {
        $this->_initPageLayouts();

        if (isset($this->_pageLayouts[$layoutCode])) {
            return $this->_pageLayouts[$layoutCode];
        }

        return false;
    }

    /**
     * Retrieve page layout handles
     *
     * @return array
     */
    public function getPageLayoutHandles()
    {
        $handles = array();

        foreach ($this->getPageLayouts() as $layout) {
            $handles[$layout->getCode()] = $layout->getLayoutHandle();
        }

        return $handles;
    }
}