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



class Mirasvit_SearchAutocomplete_Helper_Validator extends Mirasvit_MstCore_Helper_Validator_Abstract
{
    public function testTablesExists()
    {
        $result = self::SUCCESS;
        $title = 'Search Autocomplete: Conflicts with similar extensions';
        $description = array();

        $modules = array_keys((array) Mage::getConfig()->getNode('modules')->children());

        foreach ($modules as $module) {
            if (stripos($module, 'autocomplete') !== false && $module != 'Mirasvit_SearchAutocomplete') {
                $result = self::FAILED;
                $description[] = "Another Search Autocomplete extension '$module' installed, please remove it.";
            }

            if ($module == 'Smartwave_CatalogCategorySearch') {
                $result = self::FAILED;
                $description[] = "Another Search Autocomplete extension '$module' installed, please remove it.";
            }

            if ($module == 'Webdziner_Ajaxsearch') {
                $result = self::FAILED;
                $description[] = "Another Search Autocomplete extension '$module' installed, please remove it.";
            }
        }

        return array($result, $title, $description);
    }
}
