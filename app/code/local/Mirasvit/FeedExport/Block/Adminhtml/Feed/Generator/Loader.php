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


class Mirasvit_FeedExport_Block_Adminhtml_Feed_Generator_Loader extends Mage_Adminhtml_Block_Template
{
    public function _prepareLayout()
    {
        $this->setTemplate('mirasvit/feedexport/feed/generator/loader.phtml');

        return parent::_prepareLayout();
    }

    public function getStateMessage()
    {
        return nl2br($this->getModel()->getGenerator()->getState()->toHtml());
    }

    public function getStatus()
    {
        return $this->getModel()->getGenerator()->getState()->getStatus();
    }

    public function getModel()
    {
        return Mage::registry('current_model');
    }
}