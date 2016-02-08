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



/**
 * @category Mirasvit
 */
class Mirasvit_SearchAutocomplete_Helper_Help extends Mirasvit_MstCore_Helper_Help
{
    protected $_help = array(
        'system' => array(
            'general_min_chars' => '',
            'general_delay' => 'Time delay before start search (milliseconds)',
            'general_max_results' => '',
            'general_tip' => '',
            'general_show_price' => '',
            'general_show_image' => '',
            'general_image_size' => 'Thumbnail image size. Format WIDTHxHEIGHT',
            'general_show_short_description' => '',
            'general_short_description_len' => '',
            'general_categories' => 'By default extension display in dropdown list all active top level categories.
                If you need shows another categories, you can select them from list.',
            'general_indexes' => 'By default autocomplete shows only products. You can include any enabled index to autocomlete results.
                Total number of results always less or equal than "Maximum number of results in the dropdown list"',
        ),
    );
}
