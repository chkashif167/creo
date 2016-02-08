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



$installer = $this;
$installer->startSetup();

Mage::app()->getStore()->resetConfig();

//searchsphinx advanced
copyConfigData($this, 'advanced/host',               'general/host');
copyConfigData($this, 'advanced/port',               'general/port');
copyConfigData($this, 'advanced/host',               'general/external_host');
copyConfigData($this, 'advanced/port',               'general/external_port');
copyConfigData($this, 'advanced/bin_path',           'general/bin_path');
copyConfigData($this, 'advanced/stopwords',          'advanced/stopword');
copyConfigData($this, 'advanced/synonyms',           'advanced/synonym');
copyConfigData($this, 'advanced/wildcard_exception', 'advanced/wildcard_exception');
copyConfigData($this, 'advanced/notwords',           'advanced/notword');

copyConfigData($this, 'dev/search_template',         'advanced/search_template');
copyConfigData($this, 'dev/wildcard',                'advanced/wildcard');

copyConfigData($this, 'manage/search_engine',        'general/search_engine');
copyConfigData($this, 'manage/path',                 'general/external_path');

function copyConfigData($inst, $from, $to)
{
    $value = Mage::getStoreConfig('searchsphinx/'.$from);
    if ($value) {
        $inst->setConfigData('searchsphinx/'.$to, $value);
    }
}

$installer->endSetup();
