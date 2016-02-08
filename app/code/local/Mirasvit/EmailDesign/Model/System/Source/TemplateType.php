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
 * @package   Follow Up Email
 * @version   1.0.2
 * @build     564
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_EmailDesign_Model_System_Source_TemplateType
{
    public function toOptionArray()
    {
        $result = array(
            array(
                'label' => 'HTML',
                'value' => Mirasvit_EmailDesign_Model_Design::TEMPLATE_TYPE_HTML,
            ),
            array(
                'label' => 'Text',
                'value' => Mirasvit_EmailDesign_Model_Design::TEMPLATE_TYPE_TEXT,
            ),
        );

        return $result;
    }

    public function toOptions()
    {
        $options = array();

        foreach ($this->toOptionArray() as $item) {
            $options[$item['value']] = $item['label'];
        }

        return $options;
    }
}