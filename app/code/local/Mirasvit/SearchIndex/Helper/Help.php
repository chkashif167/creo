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



class Mirasvit_SearchIndex_Helper_Help extends Mirasvit_MstCore_Helper_Help
{
    protected $_help = array(
        'index_edit_form' => array(
            'index_code' => 'The type of content for searching',
            'title' => 'Title of this index in the search results.',
            'position' => 'Position of this index in the search results tabs.',
            'is_active' => '',
        ),

        'index_edit_index_mage_catalog_product_additional' => array(
            'include_category' => 'If option enabled, customer can find a product by it\'s parent categories.',
            'include_bundled' => 'If option enabled, customer can find bundled or grouped product by information from it\'s associated products.',
            'include_tag' => 'If option enabled, customer can find product by related tags.',
            'include_id' => 'If option enabled, customer can find product by product id (entity_id).',
            'include_custom_options' => 'If option enabled, customer can find product by product custom options (SKU and title)',
            'out_of_stock_to_end' => 'If option enabled, "out of stock" products will be displayed at end of list',
        ),

        'index_edit_index_mage_cms_page_additional' => array(
            'ignore' => 'Pages that should be excluded from the search index (ex. 404 Not Found, Home Page etc)',
        ),

        'index_edit_index_external_database' => array(
            'db_connection_name' => 'Database connection name defined at app/etc/local.xml (ex. wordpress_setup)',
            'db_table_prefix' => 'Table prefix for connection. Leave empty, if tables without prefix (ex. wp_, mag_)',
        ),

        'index_edit_index_external_url' => array(
            'url_template' => 'Template of url for search index entity (ex. http://example.com/blog/?p={column}, where {column} is column of primary table ({id}, {post_id}, {thread_id}, {title}, {url_key} etc)',
        ),
    );
}
