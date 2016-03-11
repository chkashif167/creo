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


abstract class Mirasvit_FeedExport_Model_Feed_Generator_Action_Iterator_Abstract extends Varien_Object
{
    abstract public function init();
    abstract public function getCollection();
    abstract public function callback($row);
    abstract public function save($result);
    abstract public function start();
    abstract public function finish();
}