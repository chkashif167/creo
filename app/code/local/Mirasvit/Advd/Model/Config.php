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



class Mirasvit_Advd_Model_Config
{
    public function getWidgets()
    {
        $result = array();

        $path = Mage::getModuleDir('', 'Mirasvit_Advd') . DS . 'Block' . DS . 'Adminhtml' . DS . 'Widget';
        $io = new Varien_Io_File();
        $io->open();
        $io->cd($path);

        foreach ($io->ls(Varien_Io_File::GREP_DIRS) as $dir) {
            if ($dir['text'] == 'Abstract') {
                continue;
            }
            $io->cd($dir['id']);

            foreach ($io->ls(Varien_Io_File::GREP_FILES) as $widget) {
                $blockPath = strtolower('advd/adminhtml_widget_' . $dir['text']
                    . '_' . str_replace('.php', '', $widget['text']));
                $block = Mage::app()->getLayout()->createBlock($blockPath);

                if ($block && $block->isEnabled()) {
                    $result[$blockPath] = $block;
                }
            }
        }

        return $result;
    }

    public function isReplaceDashboardLink()
    {
        return Mage::getStoreConfig('advr/view/replace_dashboard_link');
    }
}
