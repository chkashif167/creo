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


class Mirasvit_Email_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function setVar($code, $value)
    {
        $variable = Mage::getModel('core/variable');
        $variable = $variable->loadByCode($code);

        if (!$variable->getId()) {
            $variable->setCode($code)
                ->setName($code);
        }

        $variable->setPlainValue($value)
            ->setHtmlValue($variable->getPlainValue())
            ->save();

        return $variable;
    }

    public function getVar($code)
    {
        $variable = Mage::getModel('core/variable');
        $variable = $variable->loadByCode($code);

        return $variable->getPlainValue();
    }

    public function prepareQueueContent($queue)
    {
        $trigger = $queue->getTrigger();
        $content = $queue->getData('content');

        if ($trigger->getGaSource() && $trigger->getGaMedium() && $trigger->getGaName()) {
            $ga = array();
            $ga[] = 'utm_source='.rawurlencode($trigger->getGaSource());
            $ga[] = 'utm_medium='.rawurlencode($trigger->getGaMedium());
            $ga[] = 'utm_campaign='.rawurlencode($trigger->getGaName());
            if ($trigger->getGaTerm() != '') {
                $ga[] = 'utm_term='.rawurlencode($trigger->getGaTerm());
            }
            if ($trigger->getGaContent() != '') {
                $ga[] = 'utm_content='.rawurlencode($trigger->getGaContent());
            }

            $content = $this->addParamsToLinks($content, $ga);
        }

        $queue->setData('content', $content);

        return true;
    }

    public function addParamsToLinks($text, $params)
    {
        if (is_array($params)) {
            $params = implode('&', $params);
        }

        $matches = array();
        if(preg_match_all('/<a\s[^>]*href=([\"\']??)([^\" >]*?)\\1[^>]*>(.*)<\/a>/siU', $text, $matches)) {
            foreach ($matches[2] as $key => $link) {
                $components = parse_url($link);
                $newLink = false;
                if (isset($components['path']) && isset($components['host'])) {
                    if (isset($components['query'])) {
                        $newLink = $link.'&'.$params;
                    } else {
                        $newLink = $link.'?'.$params;
                    }
                }

                if (isset($components['fragment'])) {
                    $newLink = str_replace('#'.$components['fragment'], '', $newLink).'#'.$components['fragment'];
                }


                if ($newLink) {
                    $from = $matches[0][$key];
                    $to   = str_replace('href="'.$link.'"', 'href="'.$newLink.'"', $from);
                    
                    $text = str_replace($from, $to, $text);
                }
            }
        }

    
        return $text;
    }

    public static function determineEmails($emails)
    {
        if (!$emails) {
            return array();
        }

        $emails = trim(str_replace(array(',', ';'), ' ', $emails));
        do {
            $emails = str_replace('  ', ' ', $emails);
        } while (strpos($emails, '  ') !== false);

        $result = explode(' ', $emails);
        
        return $result;
    }

    public static function isWBTABInstalled()
    {
        $modules = (array)Mage::getConfig()->getNode('modules')->children();
        return array_key_exists('AW_Relatedproducts', $modules)
            && 'true' == (string)$modules['AW_Relatedproducts']->active;
    }

    public static function isARP2Installed()
    {
        $modules = (array)Mage::getConfig()->getNode('modules')->children();
        return array_key_exists('AW_Autorelated', $modules)
            && 'true' == (string)$modules['AW_Autorelated']->active;
    }
}