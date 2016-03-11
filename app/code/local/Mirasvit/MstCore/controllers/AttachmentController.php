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


class Mirasvit_MstCore_AttachmentController extends Mage_Core_Controller_Front_Action
{
	public function downloadAction()
	{
        $uid         = $this->getRequest()->getParam('uid');
        $attachments = Mage::getModel('mstcore/attachment')->getCollection()
        				->addFieldToFilter('uid', $uid);
        if (!$attachments->count()) {
            die('permission error');
        }
        $attachment = $attachments->getFirstItem();
        // give our picture the proper headers...otherwise our page will be confused
        header("Content-Disposition: attachment; filename=\"{$attachment->getName()}\"");
        header("Content-length: {$attachment->getSize()}");
        header("Content-type: {$attachment->getType()}");
        echo $attachment->getBody();
        die;
	}
}
