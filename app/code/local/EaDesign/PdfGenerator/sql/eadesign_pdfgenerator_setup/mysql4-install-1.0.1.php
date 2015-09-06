<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of mysql4-install-1
 *
 * @author Ea Design
 */
$installer = $this;

$installer->startSetup();

$installer->run("
 
DROP TABLE IF EXISTS {$this->getTable('eadesign/pdfgenerator')};
CREATE TABLE {$this->getTable('eadesign/pdfgenerator')} (
  `pdftemplate_id` int(11) unsigned NOT NULL auto_increment,
  `pdftemplate_name` varchar(255) NOT NULL default '',
  `pdftemplate_desc` varchar(255) NOT NULL default '',
  `pdftemplate_body` mediumtext NOT NULL default '',
  `pdft_type` varchar(255) NOT NULL default '',
  `pdft_filename` varchar(100) NOT NULL default '',
  `pdftp_format` varchar(255) NOT NULL default '',
  `pdft_orientation` varchar(255) NOT NULL default '',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`pdftemplate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

$installer->endSetup();