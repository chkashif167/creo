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


class Mirasvit_SearchIndex_Helper_Validator extends Mirasvit_MstCore_Helper_Validator_Abstract
{
    public function testConflictExtensions()
    {
        $result = self::SUCCESS;
        $title = 'Search Index: Conflicts with another extensions';
        $description = array();

        if (Mage::helper('mstcore')->isModuleInstalled('Sonassi_FastSearchIndex')) {
            $result = self::FAILED;
            $description[] = "Sonassi_FastSearchIndex is installed. Please disable this extension";
        }

        if (Mage::helper('mstcore')->isModuleInstalled('Activo_CatalogSearch')) {
            $result = self::FAILED;
            $description[] = "Activo_CatalogSearch is installed. Please disable this extension";
        }

        if (Mage::helper('mstcore')->isModuleInstalled('Netzarbeiter_GroupsCatalog2')) {
            $result = self::FAILED;
            $description[] = "Netzarbeiter_GroupsCatalog2 is installed. Please disable this extension or solve conflicts between collection models.";
        }

        return array($result, $title, $description);
    }
}
