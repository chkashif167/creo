<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Items
 *
 * @author Ea Design
 */
class EaDesign_PdfGenerator_Helper_Items extends Mage_Core_Helper_Abstract
{

    /**
     * Get the part for the item processing system
     * @param string $src
     * @param string $start
     * @param string $end
     * @return string
     */
    public function getTheItemsFromBetwin($src, $start, $end)
    {
        $txt = explode($start, $src);
        $txt2 = explode($end, $txt[1]);
        return trim($txt2[0]);
    }

    /**
     *
     * @return \SimpleHtmlDoom_SimpleHtmlDoomLoad
     */
    public function getTheSimpleHtmlDom()
    {
        $htmlProcessor = new SimpleHtmlDoom_SimpleHtmlDoomLoad;
        return $htmlProcessor;
    }

    /**
     * Process the items html.
     * @param string $tempmplateForHtmlProcess
     * @return string
     */
    public function processHtml($tempmplateForHtmlProcess)
    {
        $htmlProcessor = $this->getTheSimpleHtmlDom()
            ->load($tempmplateForHtmlProcess);

        foreach ($htmlProcessor->find('tr') as $e) {

            $numtd = $e->find('td');
            $td = count($numtd);
            if ($td == 1) {
                $e->innertext = '';
                $delteTd = true;
            }
            foreach ($htmlProcessor->find('td') as $e) {
                $plaintext = $e->innertext;
                if ($plaintext == EaDesign_PdfGenerator_Model_Entity_Pdfgenerator::THE_START) {
                    $e->parent->outertext = '';
                }
                if ($plaintext == EaDesign_PdfGenerator_Model_Entity_Pdfgenerator::THE_END) {
                    $e->parent->outertext = '';
                }
            }
        }
        $htmlProcessorFinish = $htmlProcessor;
        return $htmlProcessorFinish;
    }

    /**
     * Retrieve item options
     *
     * @return array
     */
    public function getItemOptions($options)
    {
        $result = array();
        if ($options) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }

        /* Will will be able to split in three */

        foreach ($result as $option => $value) {
            $data .= $value['label'] . ' - ' . $value['value'] . ' ';
        }
        if (isset($data)) {
            $productOptionesLabeled = array(
                'product_options' => array(
                    'value' => $data,
                    'label' => Mage::helper('pdfgenerator')->__('Product options')
                )
            );
        }
        return $productOptionesLabeled;
    }

    public function substrCount($haystack, $needle)
    {
        if (isset($haystack) && isset($needle)) {
            return substr_count($haystack, $needle);
        }
        return false;
    }

}

