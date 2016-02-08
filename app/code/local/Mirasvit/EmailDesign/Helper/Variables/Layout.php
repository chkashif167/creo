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


class Mirasvit_EmailDesign_Helper_Variables_Layout
{
    public function getLayoutHtml($parent, $args)
    {
        $handle = $args[0];
        $params = array();
        if (isset($args[1])) {
            $params = $args[1];
        }

        $layout = Mage::getModel('core/layout');

        if (isset($params['area'])) {
            $layout->setArea($params['area']);
        }
        else {
            $layout->setArea(Mage::app()->getLayout()->getArea());
        }

        $layout->getUpdate()->addHandle($handle);
        $layout->getUpdate()->load();

        $layout->generateXml();
        $layout->generateBlocks();

        foreach ($layout->getAllBlocks() as $blockName => $block) {
            foreach ($params as $k => $v) {
                $block->setDataUsingMethod($k, $v);
            }
        }

        $allBlocks = $layout->getAllBlocks();
        $firstBlock = reset($allBlocks);
        if ($firstBlock) {
            $layout->addOutputBlock($firstBlock->getNameInLayout());
        }

        $layout->setDirectOutput(false);
        return $layout->getOutput();
    }
}