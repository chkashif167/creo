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


class Mirasvit_FeedExport_Model_System_Config_Source_Template
{
    public function toOptionArray($filesystem = false)
    {
        $result = array();

        if ($filesystem) {
            $path = Mage::getSingleton('feedexport/config')->getTemplatePath();
            if ($handle = opendir($path)) {
                while (false !== ($entry = readdir($handle))) {
                    if (substr($entry, 0, 1) != '.') {
                        $result[] = array(
                            'label' => $entry,
                            'value' => $path.DS.$entry
                        );
                    }
                }
                closedir($handle);
            }
        } else {
            $collection = Mage::getModel('feedexport/template')->getCollection();
            $result[] = array('label' => 'Empty Template', 'value' => '');
            foreach ($collection as $template) {
                $result[] = array(
                    'label' => $template->getName().' ('.$template->getType().')',
                    'value' => $template->getId(),
                );
            }
        }
        sort($result);

        return $result;
    }
}