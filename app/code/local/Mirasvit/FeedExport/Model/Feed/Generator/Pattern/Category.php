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


class Mirasvit_FeedExport_Model_Feed_Generator_Pattern_Category
    extends Mirasvit_FeedExport_Model_Feed_Generator_Pattern
{
    public function getValue($pattern, $category)
    {
        $value = null;
        $pattern = $this->parsePattern($pattern);

        switch($pattern['key']) {
            case 'url':
                $value = $category->getUrl(false);
                break;

            case 'category_path':
                foreach ($category->getParentCategories() as $parent) {
                    $value .= $parent->getName() . ' > ';
                }
                $value = rtrim($value, ' > ');
                break;

            default:
                $value = $category->getData($pattern['key']);
        }

        $value = $this->applyFormatters($pattern, $value);

        return $value;
    }
}