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


class Mirasvit_Email_Model_System_Source_Event
{ 
    public static function toArray()
    {
        $result = array();

        $path = Mage::getModuleDir('', 'Mirasvit_Email').DS.'Model'.DS.'Event';
        $io   = new Varien_Io_File();
        $io->open();
        $io->cd($path);
        
        foreach ($io->ls(Varien_Io_File::GREP_DIRS) as $entity) {
            $io->cd($entity['id']);
            foreach ($io->ls(Varien_Io_File::GREP_FILES) as $event) {
                if ($event['filetype'] != 'php') {
                    continue;
                } elseif (strtolower($entity['text']) === 'rma') {
                    if (!Mage::helper('mstcore')->isModuleInstalled('Mirasvit_Rma')) {
                        continue;
                    }
                }

                $info      = pathinfo($event['text']);
                $eventCode = strtolower($entity['text'].'_'.$info['filename']);
                $event     = Mage::helper('email/event')->getEventModel($eventCode);
                foreach ($event->getEvents() as $code => $name) {
                    $result[$event->getEventsGroup()][$code] = $name;
                }  
            }
        }

        return $result;
    }

    public static function toOptionArray($options = null)
    {
        if ($options == null) {
            $options = self::toArray();
        }

        $result  = array();

        foreach ($options as $key => $value) {
            if (is_array($value)) {
                $result[] = array(
                    'label' => $key,
                    'value' => self::toOptionArray($value),
                );
            } else {
                $result[] = array(
                    'label' => $value,
                    'value' => $key,
                );
            }
        }
 
        return $result;
    }
}