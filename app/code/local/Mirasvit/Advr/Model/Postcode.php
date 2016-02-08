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
 * @package   Advanced Reports
 * @version   1.0.1
 * @build     539
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_Advr_Model_Postcode extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('advr/postcode');
    }

    public function __toString()
    {
        return $this->getCountryId()
        . ': ' . $this->getPostcode()
        . ' / ' . $this->getState()
        . ' / ' . $this->getProvince()
        . ' / ' . $this->getPlace()
        . ' / ' . $this->getCommunity();
    }

    public function loadByCode($countryId, $postcode)
    {
        $postcode = Mage::helper('advr/geo')->formatPostcode($postcode);

        $model = $this->getCollection()
            ->addFieldToFilter('country_id', $countryId)
            ->addFieldToFilter('postcode', $postcode)
            ->getFirstItem();

        if ($model->getPostcodeId()) {
            return $model;
        }

        return false;
    }

    public function copyUnknown($verbose = false)
    {
        $collection = Mage::getModel('sales/order_address')->getCollection();

        $collection->setPageSize(100);

        $pages = $collection->getLastPageNumber();
        $page = 1;

        do {
            $collection->setCurPage($page);
            $collection->load();

            foreach ($collection as $row) {
                $countryId = $row->getCountryId();
                $postcode = $row->getPostcode();

                if (trim($postcode) == '' || trim($countryId) == '') {
                    continue;
                }

                if (!$this->loadByCode($countryId, $postcode)) {
                    $model = Mage::getModel('advr/postcode');
                    $model->setCountryId($countryId)
                        ->setPostcode($postcode)
                        ->save();

                    if ($verbose) {
                        echo $model . PHP_EOL;
                    }
                }
            }

            $page++;
            $collection->clear();
        } while ($page <= $pages);
    }

    public function batchUpdate($verbose = false)
    {
        $limit = 100;

        $lastId = 0;

        do {
            $collection = $this->getCollection()
                ->addFieldToFilter('postcode_id', array('gt' => $lastId))
                ->setOrder('postcode_id', 'asc');

            $collection->getSelect()
                ->where('(original NOT LIKE "%google%" OR original IS NULL)')
                ->where('country_id <> "GB"')
                ->where('updated = 0');


            $collection->setPageSize(10);

            $collection->load();

            $toUpdate = array();
            foreach ($collection as $row) {
                $toUpdate[$row->getId()] = $row->getCountryId() . ': ' . $row->getPostcode();
                $lastId = $row->getPostcodeId();
            }

            $resultsGoogle = Mage::helper('advr/geo')->findInGoogle($toUpdate);

            foreach ($resultsGoogle as $id => $rows) {
                $model = $collection->getItemById($id);

                $original = $model->getData('original');
                $original = json_decode($original, true);
                $original['google'] = $rows;

                $model->setOriginal(json_encode($original))
                    ->save();

                $limit--;

                if ($verbose) {
                    echo $model . PHP_EOL;
                }
            }

        } while ($collection->count() > 0 && $limit > 0);
    }

    public function batchMerge($verbose = false)
    {
        $lastId = 0;

        do {
            $collection = $this->getCollection()
                ->addFieldToFilter('postcode_id', array('gt' => $lastId))
                ->setOrder('postcode_id', 'asc');
            $collection->getSelect()
                ->where('original LIKE "%google%"')
                ->where('updated = 0');

            $collection->setPageSize(100);

            $collection->load();

            foreach ($collection as $model) {
                $data = json_decode($model->getOriginal(), true);

                $google = array(
                    'state'     => false,
                    'province'  => false,
                    'place'     => false,
                    'community' => false,
                    'lat'       => false,
                    'lng'       => false,
                );

                foreach ($data['google'][0]['address_components'] as $component) {
                    if ($component['types'][0] == 'locality') {
                        $google['place'] = $component['long_name'];
                    }
                    if ($component['types'][0] == 'administrative_area_level_1') {
                        $google['state'] = $component['long_name'];
                    }
                    if ($component['types'][0] == 'administrative_area_level_2') {
                        $google['province'] = $component['long_name'];
                    }

                }

                $google['lat'] = $data['google'][0]['geometry']['location']['lat'];
                $google['lng'] = $data['google'][0]['geometry']['location']['lng'];

                $result = $google;

                $model->setState($result['state'])
                    ->setProvince($result['province'])
                    ->setPlace($result['place'])
                    ->setCommunity($result['community'])
                    ->setLat($result['lat'])
                    ->setLng($result['lng'])
                    ->setUpdated(1)
                    ->save();

                if ($verbose) {
                    echo $model . PHP_EOL;
                }

                $lastId = $model->getId();
            }
        } while ($collection->count() > 0);
    }

    /**
     * Export postcodes to csv geo files (per country, per 100k rows in files)
     */
    public function exportAll($verbose = false)
    {
        $countries = Mage::getResourceModel('directory/country_collection');
        foreach ($countries as $country) {
            $page = 1;
            $pageSize = 100000;
            $collection = $this->getCollection()
                ->addFieldToFilter('country_id', $country->getCountryId())
                ->setOrder('postcode', 'asc')
                ->setPageSize($pageSize)
                ->setCurPage($page);

            if ($collection->getSize() > 100) {
                while (($page - 1) * $pageSize < $collection->getSize()) {
                    if ($verbose) {
                        echo $country->getCountryId() . '...';
                    }

                    $file = 'GEO_' . $country->getCountryId() . '_' . $page . '.csv';
                    $csv = new Varien_File_Csv();
                    $csv->setDelimiter(',');
                    $csv->setEnclosure('"');

                    $data = array();
                    foreach ($collection as $postcode) {
                        $row = array(
                            $postcode->getCountryId(),
                            $postcode->getPostcode(),
                            $postcode->getPlace(),
                            $postcode->getState(),
                            $postcode->getProvince(),
                            $postcode->getCommunity(),
                            $postcode->getLat(),
                            $postcode->getLng(),
                        );
                        $data[] = $row;
                    }

                    $csv->saveData(Mage::getSingleton('advr/config')->getGeoFilesPath() . DS . $file, $data);

                    if ($verbose) {
                        echo 'done (' . ($page * $pageSize) . ' / ' . $collection->getSize() . ')' . PHP_EOL;
                    }
                    $page++;
                    $collection->setCurPage($page)
                        ->clear();
                }
            }
        }
    }

    /**
     * Import csv geo file to database
     */
    public function importFile($filePath)
    {
        $resource = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_write');

        $csv = new Varien_File_Csv();
        $data = $csv->getData($filePath);

        foreach ($data as $item) {
            $row = array(
                'country_id' => $item[0],
                'postcode'   => $item[1],
                'place'      => $item[2],
                'state'      => $item[3],
                'province'   => $item[4],
                'community'  => $item[5],
                'lat'        => $item[6],
                'lng'        => $item[7],
                'updated'    => 1,
            );

            $rows[] = $row;
            $keys[] = $row['country_id'] . $row['postcode'];

            if (count($rows) > 100) {
                $deleteCondition = array($connection->quoteInto('CONCAT_WS("", country_id, postcode) IN (?)', $keys));

                $connection->delete($resource->getTableName('advr/postcode'), $deleteCondition);

                $connection->insertMultiple(
                    $resource->getTableName('advr/postcode'),
                    $rows
                );

                $rows = array();
                $keys = array();
            }
        }

        if (count($rows)) {
            $deleteCondition = array($connection->quoteInto('CONCAT_WS("", country_id, postcode) IN (?)', $keys));

            $connection->delete($resource->getTableName('advr/postcode'), $deleteCondition);

            $connection->insertMultiple(
                $resource->getTableName('advr/postcode'),
                $rows
            );
        }

        return true;
    }

    /**
     * Return number of undefined (not in postcode table) postal codes
     */
    public function getNumberOfUnknown()
    {
        $resource = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_read');

        $select = $connection->select()->from(
            array('sales_order_address_table' => $resource->getTableName('sales/order_address')),
            array('COUNT(*)')
        )->joinLeft(
            array('advr_postcode_table' => $resource->getTableName('advr/postcode')),
            'advr_postcode_table.postcode = REPLACE(REPLACE(sales_order_address_table.postcode, " ", ""), "-","")
                    AND advr_postcode_table.country_id = sales_order_address_table.country_id',
            array()
        )
            ->where('postcode_id IS NULL or advr_postcode_table.updated=0')
            ->where('sales_order_address_table.postcode IS NOT NULL')
            ->limit(1);

        return intval($connection->fetchOne($select));
    }
}
