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


class Mirasvit_FeedExport_Helper_Format extends Mage_Core_Helper_Abstract
{
    public function preparePostData($object)
    {
        if ($object->getType() == 'csv' || $object->getType() == 'txt') {
            $format = $object->getData('csv');
            if (is_array($format)) {
                $object->setFormat(serialize($format));
            }
        } elseif ($object->getType() == 'xml') {
            if ($object->hasData('xml')) {
                $format = $object->getData('xml');
                $object->setFormat($format['format']);
            }
        }

        return $object;
    }

    public function expandFormat($object)
    {
        if ($object->getType() == 'csv' || $object->getType() == 'txt') {
            $format = unserialize($object->getData('format'));

            $xmlFormat = $this->csvFormatToXml($format);
            $object->setXmlFormat($xmlFormat);

            if (!is_array($format)) {
                $format = array();
            }
            
            foreach ($format as $key => $value) {
                $object->setData($key, $value);
            }

            $mapping = $object->getMapping();
            $map = array();
            for ($i = 0; $i < count($mapping['header']); $i++) {
                $item = array(
                    'header' => $mapping['header'][$i],
                    'prefix' => $mapping['prefix'][$i],
                    'suffix' => $mapping['suffix'][$i],
                    'type'   => $mapping['type'][$i],
                    'value'  => ($mapping['value_pattern'][$i] ? $mapping['value_pattern'][$i] : $mapping['value_attribute'][$i]),
                    'limit'  => $mapping['limit'][$i],
                );
                $map[] = new Varien_Object($item);
            }
            $object->setMap($map);
        } elseif ($object->getType() == 'xml') {
            $format = $object->getData('format');
            $object->setXmlFormat($format);
        }

        return $object;
    }

    /**
     * Convert string format to array
     * @param  string $format
     * @return array
     */
    public function parseFormat($format)
    {
        $result = array();
        $result['entity'] = array();

        preg_match_all('/{(each.*?)}*{\/each}/is', $format, $matches);

        foreach ($matches[0] as $match) {
            $type = array();
            preg_match('/{each type="(.*?)"}/is', $match, $type);
            $type = $type[1];

            $template = explode(PHP_EOL, $match);
            unset($template[0]);
            unset($template[count($template)]);
            $template = implode(PHP_EOL, $template);

            $result['entity'][$type] = $template;

            $format = str_replace($match, '%%%'.$type.'%%%', $format);
        }

        $result['template'] = $format;

        return $result;
    }

    public function csvFormatToXml($format)
    {
        $xml       = '';
        $delimiter = $format['delimiter'];
        if (!$delimiter) {
            $delimiter = ",";
        }
        if ($delimiter == 'tab') {
            $delimiter = "\t";
        }
        $enclosure = $format['enclosure'];
        if (isset($format['mapping'])) {
            $mapping   = $format['mapping'];
        }

        if ($format['extra_header']) {
            $xml .= $format['extra_header'].PHP_EOL;
        }

        if ($format['include_header']) {
            $columns = array();
            if (isset($format['mapping'])) {
                foreach ($format['mapping']['header'] as $indx => $header) {
                    $columns[] = $header;
                }
            }
            $xml .= implode($delimiter, $columns).PHP_EOL;
        }

        if (isset($mapping)) {
            if (!is_array($mapping['type'])) {
                $mapping['type'] = array();
            }
        }

        $xml .= '{each type="product"}'.PHP_EOL;
        $columns = array();
        if (isset($mapping)) {
            foreach ($mapping['type'] as $indx => $type) {
                $pattern = $enclosure;

                $pattern .= $mapping['prefix'][$indx];

                if ($type == 'attribute' || $type == 'parent_attribute') {
                    if ($mapping['value_attribute'][$indx]) {
                        $pattern .= '{'.$mapping['value_attribute'][$indx];
                        if ($type == 'parent_attribute') {
                            $pattern .= '|parent';
                        }
                        if ($mapping['limit'][$indx]) {
                            $pattern .= ', [substr 0 '.$mapping['limit'][$indx].']';
                        }
                        if (isset($mapping['formatters']) && $mapping['formatters'][$indx]) {
                            switch ($mapping['formatters'][$indx]) {
                                case 'strip_tags':
                                    $pattern .= ', [strip_tags]';
                                    break;

                                case 'intval':
                                    $pattern .= ', [intval]';
                                    break;

                                case 'price':
                                    $pattern .= ', [number_format 2 . ]';
                                    break;
                            }
                        }
                        $pattern .= ', [csvPretty '.$format['delimiter'].']';
                        $pattern .= '}';
                    }
                } else {
                    $pattern .= $mapping['value_pattern'][$indx];
                }

                $pattern .= $mapping['suffix'][$indx];
                $pattern .= $enclosure;
                $columns[] = $pattern;
            }
        }
        $xml .= implode($delimiter, $columns).PHP_EOL;
        $xml .= '{/each}';
        // echo $xml;die();
        return $xml;
    }

}