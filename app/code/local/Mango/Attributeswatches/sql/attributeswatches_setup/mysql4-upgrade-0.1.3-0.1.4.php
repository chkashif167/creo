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

$this->_conn->addColumn($this->getTable('attributeswatches'), 'attribute', 'varchar(255)');
/*$this->_conn->addColumn($this->getTable('sales_flat_quote'), 'base_lof_tax_amount', 'decimal(12,4)');
$this->_conn->addColumn($this->getTable('sales_flat_quote_address'), 'lof_tax_amount', 'decimal(12,4)');
$this->_conn->addColumn($this->getTable('sales_flat_quote_address'), 'base_lof_tax_amount', 'decimal(12,4)');*/

/*$eav = new Mage_Eav_Model_Entity_Setup('sales_setup');

$eav->addAttribute('order', 'lof_tax_amount', array('type' => 'decimal',));
$eav->addAttribute('order', 'base_lof_tax_amount', array('type' => 'decimal'));

$eav->addAttribute('order', 'lof_tax_amount_invoiced', array('type' => 'decimal',));
$eav->addAttribute('order', 'base_lof_tax_amount_invoiced', array('type' => 'decimal'));

$eav->addAttribute('order', 'lof_tax_amount_refunded', array('type' => 'decimal',));
$eav->addAttribute('order', 'base_lof_tax_amount_refunded', array('type' => 'decimal'));

$eav->addAttribute('order', 'lof_tax_amount_canceled', array('type' => 'decimal',));
$eav->addAttribute('order', 'base_lof_tax_amount_canceled', array('type' => 'decimal'));

$eav->addAttribute('invoice', 'lof_tax_amount', array('type' => 'decimal',));
$eav->addAttribute('invoice', 'base_lof_tax_amount', array('type' => 'decimal'));

$eav->addAttribute('creditmemo', 'lof_tax_amount', array('type' => 'decimal',));
$eav->addAttribute('creditmemo', 'base_lof_tax_amount', array('type' => 'decimal'));*/

$this->endSetup();

?>
