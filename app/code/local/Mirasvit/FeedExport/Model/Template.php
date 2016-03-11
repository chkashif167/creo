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


class Mirasvit_FeedExport_Model_Template extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('feedexport/template');
    }

    public function export()
    {
        $this->setFormat64(base64_encode($this->getFormat()));
        $xml = $this->toXml(array('name', 'type', 'format64'));

        $path = Mage::getSingleton('feedexport/config')->getTemplatePath().DS.$this->getName().'.xml';

        file_put_contents($path, $xml);

        return $path;
    }

    public function import($templatePath)
    {
        $content = file_get_contents($templatePath);
        $xml = new Varien_Simplexml_Element($content);
        $template = $xml->asArray();

        $template['format'] = base64_decode($template['format64']);

        $model = $this->getCollection()->addFieldToFilter('name', $template['name'])->getFirstItem();
        $model->addData($template)
            ->save();

        return $model;
    }
}