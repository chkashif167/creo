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


abstract class Mirasvit_FeedExport_Model_Feed_Generator_Action extends Varien_Object
{
    public function start()
    {
        Mage::helper('feedexport')->getState()->startChainItem($this->getKey());
    }

    public function finish()
    {
        Mage::helper('feedexport')->getState()->finishChainItem($this->getKey());
    }

    public function setValue($key, $value)
    {
        Mage::helper('feedexport')->getState()->setChainItemValue($this->getKey(), $key, $value);

        return $this;
    }

    public function getValue($key)
    {
        return Mage::helper('feedexport')->getState()->getChainItemValue($this->getKey(), $key);
    }

    public function setIteratorType()
    {
        Mage::helper('feedexport')->getState()->setData('iterator_type', $this->getKey());

        return $this;
    }
}