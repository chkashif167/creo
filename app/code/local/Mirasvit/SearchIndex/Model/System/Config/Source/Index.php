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
 * @package   Sphinx Search Ultimate
 * @version   2.3.2
 * @build     1290
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_SearchIndex_Model_System_Config_Source_Index
{
    public function toOptionArray($exclude = true)
    {
        $result = array();
        $result['Magento'] = array('label' => 'Magento');

        $path = Mage::getModuleDir('', 'Mirasvit_SearchIndex').DS.'Model'.DS.'Index';
        $io = new Varien_Io_File();
        $io->open();
        $io->cd($path);

        foreach ($io->ls(Varien_Io_File::GREP_DIRS) as $space) {
            $io->cd($space['id']);
            foreach ($io->ls(Varien_Io_File::GREP_DIRS) as $module) {
                $io->cd($module['id']);
                foreach ($io->ls(Varien_Io_File::GREP_DIRS) as $entity) {
                    if ($io->fileExists($entity['id'].DS.'Index.php', true)) {
                        $indexCode = $space['text'].'_'.$module['text'].'_'.$entity['text'];
                        $index = Mage::helper('searchindex/index')->getIndexModel($indexCode);

                        if (is_object($index)) {
                            if ($index->canUse()) {
                                if (!isset($result[$index->getBaseGroup()])) {
                                    $result[$index->getBaseGroup()] = array(
                                        'label' => $index->getBaseGroup(),
                                        'value' => array(),
                                    );
                                }
                                $result[$index->getBaseGroup()]['value'][] = array(
                                    'value' => $index->getCode(),
                                    'label' => $index->getBaseTitle(),
                                );
                            }
                        } else {
                            Mage::throwException('Wrong model for index '.$indexCode);
                        }
                    }
                }
            }
        }

        return $result;
    }
}
