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


$triggers = Mage::getModel('email/trigger')->getCollection();

foreach ($triggers as $trigger) {
	$chains = $trigger->getChainCollection();
	foreach ($chains as $chain) {
		$delay = array(
			'days' 		=> intval($chain->getDelay() / 60 / 60 / 24) * 60 * 60 * 24,
			'hours' 	=> (intval($chain->getDelay() / 60 / 60) - intval($chain->getDelay() / 60 / 60 / 24) * 24) * 60 * 60,
			'minutes' 	=> (intval($chain->getDelay() / 60) - intval($chain->getDelay() / 60 / 60 / 24) * 24 * 60 - (intval($chain->getDelay() / 60 / 60) - intval($chain->getDelay() / 60 / 60 / 24) * 24) * 60) * 60,
			'type' 		=> 'after'
		);
		$chain->setData('delay', serialize($delay));
		$chain->save();
	}
}