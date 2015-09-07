<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Data
 *
 * @author Ea Design
 */
class EaDesign_PdfGenerator_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     *  Need to get the system on all optiones
     * @return array
     */
    public function processAllVars($varialbles = array())
    {
        /* value and label */
        $varData = array();
        foreach ($varialbles as $variable) {
            $allKeysLabel = array();
            $allKeys = array();
            $allVars = array();
            foreach (array_keys($variable) as $v) {
                if(isset($variable[$v]['label'])) {
                    $allKeysLabel['label_' . $v] = $variable[$v]['label'] . ' ' . $variable[$v]['value'];
                }
                $allKeys[$v] = $variable[$v]['value'];
            }
            $allVars = array_merge($allKeysLabel, $allKeys);
            $varData[] = $allVars;
        }
        foreach ($varData as $value) {
            foreach ($value as $key => $val) {
                $varsData[$key] = $val;
            }
        }
        return $varsData;
    }

    /**
     * Get the invoice values as array for template
     * @param type $varialble
     * @return array
     */
    public function getAsVariable($varialble = array())
    {
        $data = array();

        foreach ($varialble as $data) {
            if (isset($data['label']) && isset($data['amount'])) {
                $theData[$data['variable']] = $data['label'] . ' ' . $data['amount'];
            }
            if (isset($data['label']) && !isset($data['amount'])) {
                $theData[$data['variable']] = $data['label'];
            }
            if (!isset($data['label']) && isset($data['amount'])) {
                $theData[$data['variable']] = $data['amount'];
            }
        }
        return $theData;
    }

    /**
     *
     * @param string $templateText
     * @return Core_Model_Email_Template Object
     */
    public function setTheTemplateLayout($templateText)
    {
        $pdfProcessTemplate = Mage::getModel('core/email_template');
        $templateText = preg_replace('#\{\*.*\*\}#suU', '', $templateText);
        $pdfProcessTemplate->setTemplateText($templateText);

        return $pdfProcessTemplate;
    }

    public function arrayToStandard($variable = array())
    {
        foreach ($variable as $key => $var) {
            $variables[] = array($key => $var);
        }
        return $variables;
    }
}
