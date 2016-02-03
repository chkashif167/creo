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
 * @package   Advanced Reports
 * @version   1.0.1
 * @build     539
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */


$installer = $this;
$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS `{$this->getTable('advr/postcode')}`;
CREATE TABLE `{$this->getTable('advr/postcode')}` (
    `postcode_id`    int(11)        NOT NULL AUTO_INCREMENT,
    
    `country_id`     varchar(2)     NOT NULL,
    `postcode`       varchar(20)    NOT NULL,

    `place`          varchar(180)    NULL,
    `state`          varchar(100)    NULL,
    `province`       varchar(100)    NULL,
    `community`      varchar(100)    NULL,

    `lat`            decimal(10, 8) NOT NULL,
    `lng`            decimal(11, 8) NOT NULL,

    `updated`        int(1)         NOT NULL,

    `original`       longtext       NULL,
    PRIMARY KEY (`postcode_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

try {
    $installer->run("CREATE INDEX postcode_postcode_idx ON {$this->getTable('advr/postcode')} (postcode);");
    $installer->run("CREATE INDEX postcode_country_idx ON {$this->getTable('advr/postcode')} (country_id);");
} catch (Exception $e) {
}

$installer->endSetup();
