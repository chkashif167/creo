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


$installer = $this;
$installer->startSetup();

$this->getConnection()->modifyColumn(
	$this->getTable('email/trigger_chain'),
	'delay',
	array(
		'type' 		=> Varien_Db_Ddl_Table::TYPE_TEXT,
		'nullable'	=> true,
		'comment'	=> 'Serialized delay'
	)
);

$this->getConnection()->resetDdlCache();

$installer->endSetup();