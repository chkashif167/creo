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


class Mirasvit_EmailReport_Helper_Data extends Mirasvit_Email_Helper_Data
{
    public function prepareQueueContent($queue)
    {
        $content = $queue->getData('content');

        $info = array();
        $info[] = 'emqc='.rawurlencode($queue->getUniqKeyMd5());

        $content = $this->addParamsToLinks($content, $info);

        $openLogUrl = Mage::getUrl('emailreport/index/open', array('emqo' => $queue->getUniqKeyMd5()));
        $content .= '<img src="'.$openLogUrl.'">';

        $queue->setData('content', $content);

        return true;
    }

    public function setQueueId($queueId)
    {
        Mage::getSingleton('core/cookie')->set('emdi', $queueId, 3 * 3600);

        return true;
    }

    public function getQueueId()
    {
        return Mage::getSingleton('core/cookie')->get('emdi');
    }
}