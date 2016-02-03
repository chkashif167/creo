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



class Mirasvit_SearchSphinx_Block_Adminhtml_Synonym_Grid_Renderer_Synonyms extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $output = '';

        $synonyms = explode(',', $row->getSynonyms());
        foreach ($synonyms as $synonym) {
            $output .= '<span style="margin: 0px 15px 0px 0px;">'.$synonym.'</span>';
        }

        return $output;
    }
}
