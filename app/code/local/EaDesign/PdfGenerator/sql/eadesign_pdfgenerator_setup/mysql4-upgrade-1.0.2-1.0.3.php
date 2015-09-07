<?php

$installer = $this;

$installer->startSetup();
/**
 * General fileds
 */
$installer->getConnection()->addColumn($installer->getTable('eadesign/pdfgenerator'), 'pdft_is_active', array(
    'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
    'unsigned' => true,
    'nullable' => false,
    'default' => '0',
    'comment' => 'Active setting'
));

$installer->getConnection()->addColumn($installer->getTable('eadesign/pdfgenerator'), 'pdft_default', array(
    'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
    'unsigned' => true,
    'nullable' => false,
    'default' => '0',
    'comment' => 'Default setting'
));
/**
 * Header fields
 */
$installer->getConnection()->addColumn($installer->getTable('eadesign/pdfgenerator'), 'pdfth_header', array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'unsigned' => true,
    'nullable' => false,
    'default' => '',
    'comment' => 'Header body'
));


/**
 * Footer fields
 */
$installer->getConnection()->addColumn($installer->getTable('eadesign/pdfgenerator'), 'pdftf_footer', array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'unsigned' => true,
    'nullable' => false,
    'default' => '',
    'comment' => 'Footer body'
));


/**
 * Css fileds
 */
$installer->getConnection()->addColumn($installer->getTable('eadesign/pdfgenerator'), 'pdft_css', array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'unsigned' => true,
    'nullable' => false,
    'default' => '',
    'comment' => 'Css body'
));

/**
 * Settings fields
 */
$installer->getConnection()->addColumn($installer->getTable('eadesign/pdfgenerator'), 'pdftc_customchek', array(
    'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
    'unsigned' => true,
    'nullable' => false,
    'default' => '0',
    'comment' => 'Paper custom check'
));

$installer->getConnection()->addColumn($installer->getTable('eadesign/pdfgenerator'), 'pdft_customwidth', array(
    'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
    'scale' => 4,
    'precision' => 12,
    'nullable' => false,
    'default' => '0.0000',
    'comment' => 'Paper custom Width'
));

$installer->getConnection()->addColumn($installer->getTable('eadesign/pdfgenerator'), 'pdft_customheight', array(
    'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
    'scale' => 4,
    'precision' => 12,
    'nullable' => false,
    'default' => '0.0000',
    'comment' => 'Paper custom Height'
));


$installer->getConnection()->addColumn($installer->getTable('eadesign/pdfgenerator'), 'pdftm_top', array(
    'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
    'scale' => 4,
    'precision' => 12,
    'nullable' => false,
    'default' => '0.0000',
    'comment' => 'Paper margins top'
));

$installer->getConnection()->addColumn($installer->getTable('eadesign/pdfgenerator'), 'pdftm_bottom', array(
    'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
    'scale' => 4,
    'precision' => 12,
    'nullable' => false,
    'default' => '0.0000',
    'comment' => 'Paper margins bottom'
));

$installer->getConnection()->addColumn($installer->getTable('eadesign/pdfgenerator'), 'pdftm_left', array(
    'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
    'scale' => 4,
    'precision' => 12,
    'nullable' => false,
    'default' => '0.0000',
    'comment' => 'Paper margins left'
));

$installer->getConnection()->addColumn($installer->getTable('eadesign/pdfgenerator'), 'pdftm_right', array(
    'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
    'scale' => 4,
    'precision' => 12,
    'nullable' => false,
    'default' => '0.0000',
    'comment' => 'Paper margins right'
));


$installer->run("
    
insert into {$this->getTable('eadesign/pdfgenerator')} 
    
(`pdftemplate_id`, `pdftemplate_name`, `pdftemplate_desc`, `pdftemplate_body`, `pdft_type`, `pdft_filename`, `pdftp_format`, `pdft_orientation`, `created_time`, `update_time`, `template_store_id`, `pdft_is_active`, `pdft_default`, `pdfth_header`, `pdftf_footer`, `pdft_css`, `pdftc_customchek`, `pdft_customwidth`, `pdft_customheight`, `pdftm_top`, `pdftm_bottom`, `pdftm_left`, `pdftm_right`) VALUES

(1, 'Invoice Template', 'The template for all stores.', '<table style=\"width: 100%;\" border=\"0\">\r\n<tbody>\r\n<tr>\r\n<td>\r\n<p>{{var ea_logo_store}}</p>\r\n<p><br />Ea Desing Stodio&nbsp;by Eco Active SRL&nbsp;<br />Addres:&nbsp;<strong>Calea Chisinaului Nr. 1</strong><br />City:&nbsp;<strong>Iasi</strong><br />Region:&nbsp;<strong>Iasi</strong><br />Email:<strong>&nbsp;office@eadesign.ro</strong><br /><strong></strong>Phone:&nbsp;<strong>0332-419.707</strong><br />Country:&nbsp;<strong>Romania</strong></p>\r\n</td>\r\n<td align=\"center\">\r\n<p>Invoice :&nbsp;{{var ea_invoice_id}}</p>\r\n<p>Status:&nbsp;{{var ea_invoice_status}}</p>\r\n<p>Date:&nbsp;{{var ea_invoice_date}}</p>\r\n<p>Customer Vat:&nbsp;{{var customer_taxvat}}</p>\r\n</td>\r\n<td align=\"right\">\r\n<p style=\"text-align: right;\">Billing:</p>\r\n<p style=\"text-align: right;\">&nbsp;</p>\r\n<p style=\"text-align: right;\">{{var billing_address}}</p>\r\n<p style=\"text-align: right;\">&nbsp;</p>\r\n<p style=\"text-align: right;\">Shipping:</p>\r\n<p style=\"text-align: right;\">&nbsp;</p>\r\n<p style=\"text-align: right;\">{{var shipping_address}}</p>\r\n<p style=\"text-align: right;\">&nbsp;</p>\r\n<p style=\"text-align: right;\">Billing and shipping information:</p>\r\n<p style=\"text-align: right;\">&nbsp;</p>\r\n<p style=\"text-align: right;\">{{var billing_method}}</p>\r\n<p style=\"text-align: right;\">{{var shipping_method}}</p>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p>&nbsp;</p>\r\n<table style=\"width: 100%;\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\">\r\n<thead>\r\n<tr bgcolor=\"#c0c0c0\">\r\n<td>\r\n<p>Pos</p>\r\n</td>\r\n<td colspan=\"2\"><span>Product Name/SKU/Options</span></td>\r\n<td><span>Qty</span></td>\r\n<td>Price Inc/Exc</td>\r\n<td><span>Subtotal</span></td>\r\n<td>Discount</td>\r\n<td><span>Tax</span></td>\r\n<td>Manufacturer</td>\r\n<td colspan=\"2\">Image</td>\r\n</tr>\r\n</thead>\r\n<tbody>\r\n<tr>\r\n<td colspan=\"11\">##productlist_start##</td>\r\n</tr>\r\n<tr>\r\n<td>{{var items_position}}</td>\r\n<td colspan=\"2\">\r\n<p class=\"redname\">{{var items_name}}</p>\r\n<p>{{var bundle_items_option}}</p>\r\n<p>{{var items_sku}}</p>\r\n<p>{{var product_options}}</p>\r\n</td>\r\n<td>\r\n<p>{{var items_qty}}</p>\r\n</td>\r\n<td>\r\n<p>{{var itemcarptice}}</p>\r\n<p>{{var itemcarpticeexcl}}</p>\r\n</td>\r\n<td>\r\n<p>{{var itemcarpticeicl}}</p>\r\n<p>{{var itemsubtotal}}</p>\r\n</td>\r\n<td>{{var items_discount}}</td>\r\n<td>\r\n<p><span>{{var items_tax}}</span></p>\r\n</td>\r\n<td>{{var manufacturer}}</td>\r\n<td colspan=\"2\">{{var productimage}}&nbsp;</td>\r\n</tr>\r\n<tr>\r\n<td colspan=\"11\">##productlist_end##</td>\r\n</tr>\r\n<tr>\r\n<td colspan=\"10\">Subtotal Including Tax</td>\r\n<td>{{var subtotalincludingtax}}</td>\r\n</tr>\r\n<tr>\r\n<td colspan=\"10\">Subtotal Excluding Tax</td>\r\n<td>{{var subtotalexcludingtax}}</td>\r\n</tr>\r\n<tr>\r\n<td colspan=\"10\">Shipping&nbsp;Including Tax</td>\r\n<td>{{var shipping_amountincltax}}</td>\r\n</tr>\r\n<tr>\r\n<td colspan=\"10\">Shipping&nbsp;Excluding Tax</td>\r\n<td>{{var shipping_amount}}</td>\r\n</tr>\r\n<tr>\r\n<td colspan=\"10\">Shipping Tax</td>\r\n<td>{{var shipping_tax}}</td>\r\n</tr>\r\n<tr>\r\n<td colspan=\"10\">Discount{{depend tax_tva}}</td>\r\n<td>{{var discountammount}}</td>\r\n</tr>\r\n<tr>\r\n<td colspan=\"10\">Tax VAT (Tax name)</td>\r\n<td>{{var tax_tva}}</td>\r\n</tr>\r\n<tr>\r\n<td colspan=\"10\">{{/depend}}Total Tax</td>\r\n<td>{{var all_tax_ammount}}</td>\r\n</tr>\r\n<tr>\r\n<td colspan=\"10\">Grand total&nbsp;Including Tax</td>\r\n<td>{{var grandtotalincludingtax}}</td>\r\n</tr>\r\n<tr>\r\n<td colspan=\"10\">Grand total&nbsp;Excluding Tax</td>\r\n<td>{{var grandtotalexcludingtax}}</td>\r\n</tr>\r\n<tr>\r\n<td colspan=\"10\">Grand Tax</td>\r\n<td>{{var grandtotaltax}}&nbsp;</td>\r\n</tr>\r\n</tbody>\r\n</table>', '1', 'invoice {{var ea_invoice_id}} {{var ea_invoice_date}} {{var ea_invoice_status}} ', '0', 'portrait', '2013-05-13 08:22:18', '2013-05-14 06:48:07', 0, 1, 0, '<p>Dear customer&nbsp;{{var customer_name}}. Here is you inoice.</p>', '<p><span style=\"font-size: large;\"><strong>Page number {PAGENO}/{nbpg}.</strong> </span>Call us at 0800 454 454 at eny time!</p>', '.redname {\r\ncolor: red;\r\ntext-decoration: none;\r\n}', 0, 1.0000, 1.0000, 20.0000, 20.0000, 10.0000, 10.0000);
    
");

$installer->endSetup();



