<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mango
 * @package    Mango_Attributeswatches
 * @copyright  Copyright (c) 2010 Mango Extensions (http://www.mangoextensions.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$this->startSetup();

$this->_conn->addColumn($this->getTable('catalog_product_entity_media_gallery_value'), 'associated_attributes', 'text');

$this->endSetup();

?>
