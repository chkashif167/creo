<?php

/*
 * This script deletes duplicate images and imagerows from the database of which the images are not present in the filesystem.
 * It also removes images that are exact copies of another image for the same product.
 * And lastly, it looks for images that are on the filesystem but not in the database (orphaned images).
 * 
 * This script can most likely be optimized but since it'll probably only be run a few times, I can't be bothered.
 *
 * Place scripts in a folder named 'scripts' (or similar) in the Magento root.
 *
 * Note: needs 'fdupes' lib to run cleanDuplicates function.
 *
 */

chdir(dirname(__FILE__));

require_once './app/Mage.php';
Mage::app();
Mage::setIsDeveloperMode(true);
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(0);
ini_set('memory_limit','3048M');

$resource = Mage::getSingleton('core/resource');
$db = $resource->getConnection('core_write');

$eavAttribute = new Mage_Eav_Model_Mysql4_Entity_Attribute();
$thumbnailAttrId = $eavAttribute->getIdByCode('catalog_product', 'thumbnail');
$smallImageAttrId = $eavAttribute->getIdByCode('catalog_product', 'small_image');
$imageAttrId = $eavAttribute->getIdByCode('catalog_product', 'image');

$cleanUpDuplicates = false;
$countProductWithoutImages = false;
$cleanUpOrphans = false;
$cleanUpTableRowsMediaGallery = true;
$cleanUpTableRowsVarchar = true;
$setDefaultImageForProductsWithoutDefaultImage = true;

if($countProductWithoutImages) {
    $result = $db->fetchAll('SELECT * FROM `' . $resource->getTableName('catalog_product_entity_media_gallery') . '` as mediagallery RIGHT OUTER JOIN ' . $resource->getTableName('catalog_product_entity') . ' as entitytable ON entitytable.entity_id = mediagallery.entity_id WHERE mediagallery.value is NULL AND entitytable.entity_id IN (89697,103863,103864,103865,103866,103867,103868,103869,103870,103871,103872,103873,103874,103875,103876,103877,103878,103879,103880,103881,103882,103883,103884,103885,103886,103887,103888,103889,103890,103891,103892,103893,103894,103895,103896,103897,103898,103899,103900,103901,103902,103903,103904,103905,103906,103907,103908,103909,103910,103911,103912,103913,103914,103915,103916,103917,103918,103919,103920,103921,103922,103923,103924,103925,103926,103927,103928,103929,103930,103931,103932,103933,103934,103935,103936,103937,103938,103939,103940,103941,103942,103943,103944,103945,103946,103947,103948,103949,103950,103951,103952,103953,103954,103955,103956,103957,103958,103959,103960,103961,103962,103963,103964,103965,103966,103967,103968,103969,103970,103971,103972,103973,103974,103975,103976,103977,103978,103979,103980,103981,103982,103983,103984,103985,103986,103987,103988,103989,103990,103991,103992,103993,103994,103995,103996,103997,103998,103999,104000,104001,104002,104003,104004,104005,104006,104007,104008,104009,104010,104011,104012,104013,104014,104015,104016,104017,104018,104019,104020,104021,104022,104023,104024,104025,104026,104027,104028,104029,104030,104031,104032,104033,104034,104035,104036,104037,104038,104039,104040,104041,104042,104043,104044,104045,104046,104047,104048,104049,104050,104051,104052,104053,104054,104055,104056,104057,104058,53993,53994,53995,53996,53997,53998,53999,54000,54001,54002,54003,54004,54005,54006,54007,54008,54009,54010,54011,54012,54013,54014,54015,54016,54017,54018,54019,54020,54021,54022,54023,54024,54025,54026,54027,54028,54029,54030,54031,54032,54033,54034,54035,54036,54037,54038,54039,54040,54041,54042,54043,54044,54045,54046,54047,54048,54049,54050,54051,54052,54053,54054,54055,54056,54057,54058,54059,54060,54061,54062,54063,54064,54065,54066,54067,54068,54069,54070)');
    echo count($result) . ' products without images' . "\n";
}

if($cleanUpDuplicates) {
    $directory = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product' . DS; //. '/catalog/product/z/o/';
    
    $output = shell_exec('find ' . $directory . ' -type d -exec fdupes -n {} \;'); // find duplicates
    $before = substr(shell_exec('find ' . $directory . ' -type f | wc -l'),0,-1); // count files for difference calculation
    $total = shell_exec('du -h ' . $directory); $total = explode("\n",$total); array_pop($total); $total = array_pop($total); $total = explode("\t",$total); $total = array_shift($total);
    $totalBefore = $total;
    
    $chunks = explode("\n\n",$output);
    
    /* Run through duplicates and replace database rows */
    foreach($chunks as $chunk) {
        $files = explode("\n",$chunk);
        $original = array_shift($files);
        foreach($files as $file) {
            // update database where filename=file set filename=original
            $original = DS . implode(DS,array_slice(explode(DS,$original), -3));
            $file = DS . implode(DS,array_slice(explode(DS,$file), -3));
            $oldFileOnServer = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product' . $file;
            $newFileOnServer = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product' . $original;
            if(file_exists($newFileOnServer) && file_exists($oldFileOnServer)) {
                $db->beginTransaction();
                $resultVarchar = $db->update('catalog_product_entity_varchar', array('value'=>$original), $db->quoteInto('value =?',$file));
                $db->commit();
                $db->beginTransaction();
                $resultGallery = $db->update('catalog_product_entity_media_gallery', array('value'=>$original), $db->quoteInto('value =?',$file));
                $db->commit();
                echo 'Replaced ' . $file . ' with ' . $original . ' (' . $resultVarchar . '/' . $resultGallery . ')' . "\n";
                unlink($oldFileOnServer);
                if(file_exists($oldFileOnServer)) {
                    die('File ' . $oldFileOnServer . ' not deleted; permissions issue?');
                }
            } else {
                if(!file_exists($oldFileOnServer)) {
                    echo 'File ' . $oldFileOnServer . ' does not exist.' . "\n";
                }
                if(!file_exists($newFileOnServer)) {
                    echo 'File ' . $newFileOnServer . ' does not exist.' . "\n";
                }
            }
        }
    }
    
    $after = substr(shell_exec('find ' . $directory . ' -type f | wc -l'),0,-1); // calculate difference
    $total = shell_exec('du -h ' . $directory); $total = explode("\n",$total); array_pop($total); $total = array_pop($total); $total = explode("\t",$total); $total = array_shift($total);
    $totalAfter = $total;
    
    echo 'In directory ' . $directory . ' the script has deleted ' . ($before-$after) . ' files - went from ' . $totalBefore . ' to ' . $totalAfter . "\n";
}

if($cleanUpOrphans) {
    /* Clean up orphaned images */
    $dir = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product';
    $files = glob($dir . DS . '[A-z0-9]' . DS . '[A-z0-9]' . DS . '*');
    foreach($files as $file) {
        if(!is_file($file)) continue;
        $filename = DS . implode(DS,array_slice(explode(DS,$file),-3));
        //echo $filename."\n";
        $results = $db->fetchAll("SELECT * FROM " . $resource->getTableName('catalog_product_entity_media_gallery') . " WHERE value='".$filename."'");
        if(count($results)==0) {
            unlink($file);
            echo 'Deleting orphaned image ' . $filename . "\n";
            $deleted++;
        }
        $total++;
    }
    echo 'Deleted ' . $deleted . ' of total ' . $total;
}

if($cleanUpTableRowsMediaGallery) {
    /* Clean up images from media gallery tables */
    $images = $db->fetchAll("SELECT value,value_id FROM " . $resource->getTableName('catalog_product_entity_media_gallery') . " WHERE entity_id IN (89697,103863,103864,103865,103866,103867,103868,103869,103870,103871,103872,103873,103874,103875,103876,103877,103878,103879,103880,103881,103882,103883,103884,103885,103886,103887,103888,103889,103890,103891,103892,103893,103894,103895,103896,103897,103898,103899,103900,103901,103902,103903,103904,103905,103906,103907,103908,103909,103910,103911,103912,103913,103914,103915,103916,103917,103918,103919,103920,103921,103922,103923,103924,103925,103926,103927,103928,103929,103930,103931,103932,103933,103934,103935,103936,103937,103938,103939,103940,103941,103942,103943,103944,103945,103946,103947,103948,103949,103950,103951,103952,103953,103954,103955,103956,103957,103958,103959,103960,103961,103962,103963,103964,103965,103966,103967,103968,103969,103970,103971,103972,103973,103974,103975,103976,103977,103978,103979,103980,103981,103982,103983,103984,103985,103986,103987,103988,103989,103990,103991,103992,103993,103994,103995,103996,103997,103998,103999,104000,104001,104002,104003,104004,104005,104006,104007,104008,104009,104010,104011,104012,104013,104014,104015,104016,104017,104018,104019,104020,104021,104022,104023,104024,104025,104026,104027,104028,104029,104030,104031,104032,104033,104034,104035,104036,104037,104038,104039,104040,104041,104042,104043,104044,104045,104046,104047,104048,104049,104050,104051,104052,104053,104054,104055,104056,104057,104058,53993,53994,53995,53996,53997,53998,53999,54000,54001,54002,54003,54004,54005,54006,54007,54008,54009,54010,54011,54012,54013,54014,54015,54016,54017,54018,54019,54020,54021,54022,54023,54024,54025,54026,54027,54028,54029,54030,54031,54032,54033,54034,54035,54036,54037,54038,54039,54040,54041,54042,54043,54044,54045,54046,54047,54048,54049,54050,54051,54052,54053,54054,54055,54056,54057,54058,54059,54060,54061,54062,54063,54064,54065,54066,54067,54068,54069,54070)");
    foreach($images as $image) {
        if(!file_exists(Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product' . $image['value'])) {
            echo $image['value'] . ' does not exist; deleting.' . "\n";
            $db->query("DELETE FROM " . $resource->getTableName('catalog_product_entity_media_gallery') . " WHERE value_id = ?",array($image['value_id']));
            $db->query("DELETE FROM " . $resource->getTableName('catalog_product_entity_media_gallery_value') . " WHERE value_id = ?",array($image['value_id']));
        }
    }
}

if($cleanUpTableRowsVarchar) {
    /* Clean up images from varchar table */
    $images = $db->fetchAll("SELECT value,value_id FROM " . $resource->getTableName('catalog_product_entity_varchar') . " WHERE entity_id IN (89697,103863,103864,103865,103866,103867,103868,103869,103870,103871,103872,103873,103874,103875,103876,103877,103878,103879,103880,103881,103882,103883,103884,103885,103886,103887,103888,103889,103890,103891,103892,103893,103894,103895,103896,103897,103898,103899,103900,103901,103902,103903,103904,103905,103906,103907,103908,103909,103910,103911,103912,103913,103914,103915,103916,103917,103918,103919,103920,103921,103922,103923,103924,103925,103926,103927,103928,103929,103930,103931,103932,103933,103934,103935,103936,103937,103938,103939,103940,103941,103942,103943,103944,103945,103946,103947,103948,103949,103950,103951,103952,103953,103954,103955,103956,103957,103958,103959,103960,103961,103962,103963,103964,103965,103966,103967,103968,103969,103970,103971,103972,103973,103974,103975,103976,103977,103978,103979,103980,103981,103982,103983,103984,103985,103986,103987,103988,103989,103990,103991,103992,103993,103994,103995,103996,103997,103998,103999,104000,104001,104002,104003,104004,104005,104006,104007,104008,104009,104010,104011,104012,104013,104014,104015,104016,104017,104018,104019,104020,104021,104022,104023,104024,104025,104026,104027,104028,104029,104030,104031,104032,104033,104034,104035,104036,104037,104038,104039,104040,104041,104042,104043,104044,104045,104046,104047,104048,104049,104050,104051,104052,104053,104054,104055,104056,104057,104058,53993,53994,53995,53996,53997,53998,53999,54000,54001,54002,54003,54004,54005,54006,54007,54008,54009,54010,54011,54012,54013,54014,54015,54016,54017,54018,54019,54020,54021,54022,54023,54024,54025,54026,54027,54028,54029,54030,54031,54032,54033,54034,54035,54036,54037,54038,54039,54040,54041,54042,54043,54044,54045,54046,54047,54048,54049,54050,54051,54052,54053,54054,54055,54056,54057,54058,54059,54060,54061,54062,54063,54064,54065,54066,54067,54068,54069,54070) AND (attribute_id = ? OR attribute_id = ? OR attribute_id = ?)",array($thumbnailAttrId,$smallImageAttrId,$imageAttrId));
    foreach($images as $image) {
        if(!file_exists(Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product' . $image['value'])) {
            echo $image['value'] . ' does not exist; deleting.' . "\n";
            $db->query("DELETE FROM " . $resource->getTableName('catalog_product_entity_varchar') . " WHERE value_id = ?",array($image['value_id']));
        }
    }
}

if($setDefaultImageForProductsWithoutDefaultImage) {
    $products = $db->fetchAll('SELECT sku,entity_id FROM catalog_product_entity WHERE entity_id IN (89697,103863,103864,103865,103866,103867,103868,103869,103870,103871,103872,103873,103874,103875,103876,103877,103878,103879,103880,103881,103882,103883,103884,103885,103886,103887,103888,103889,103890,103891,103892,103893,103894,103895,103896,103897,103898,103899,103900,103901,103902,103903,103904,103905,103906,103907,103908,103909,103910,103911,103912,103913,103914,103915,103916,103917,103918,103919,103920,103921,103922,103923,103924,103925,103926,103927,103928,103929,103930,103931,103932,103933,103934,103935,103936,103937,103938,103939,103940,103941,103942,103943,103944,103945,103946,103947,103948,103949,103950,103951,103952,103953,103954,103955,103956,103957,103958,103959,103960,103961,103962,103963,103964,103965,103966,103967,103968,103969,103970,103971,103972,103973,103974,103975,103976,103977,103978,103979,103980,103981,103982,103983,103984,103985,103986,103987,103988,103989,103990,103991,103992,103993,103994,103995,103996,103997,103998,103999,104000,104001,104002,104003,104004,104005,104006,104007,104008,104009,104010,104011,104012,104013,104014,104015,104016,104017,104018,104019,104020,104021,104022,104023,104024,104025,104026,104027,104028,104029,104030,104031,104032,104033,104034,104035,104036,104037,104038,104039,104040,104041,104042,104043,104044,104045,104046,104047,104048,104049,104050,104051,104052,104053,104054,104055,104056,104057,104058,53993,53994,53995,53996,53997,53998,53999,54000,54001,54002,54003,54004,54005,54006,54007,54008,54009,54010,54011,54012,54013,54014,54015,54016,54017,54018,54019,54020,54021,54022,54023,54024,54025,54026,54027,54028,54029,54030,54031,54032,54033,54034,54035,54036,54037,54038,54039,54040,54041,54042,54043,54044,54045,54046,54047,54048,54049,54050,54051,54052,54053,54054,54055,54056,54057,54058,54059,54060,54061,54062,54063,54064,54065,54066,54067,54068,54069,54070)');
    foreach($products as $product) {
        $chooseDefaultImage = false;
        $images = $db->fetchAll('select * from catalog_product_entity_varchar where `entity_id` = ? AND (`attribute_id` = ? OR `attribute_id` = ? OR `attribute_id` = ?)', array($product['entity_id'], $imageAttrId,$smallImageAttrId,$thumbnailAttrId));
        if(count($images) == 0) {
            $chooseDefaultImage = true;
        } else {
            foreach($images as $image) {
                if($image['value']== 'no_selection') {
                    $chooseDefaultImage = true;
                    break;
                }
            }
        }
        if($chooseDefaultImage) {
            $defaultImage = $db->fetchOne('SELECT value FROM catalog_product_entity_media_gallery WHERE entity_id = ? AND attribute_id = ? LIMIT 1', array($product['entity_id'],82));
            if($defaultImage) {
                $db->query('INSERT INTO catalog_product_entity_varchar SET entity_type_id = ?, attribute_id = ?, store_id = ?, entity_id = ?, value = ? ON DUPLICATE KEY UPDATE value = ?', array(4,$imageAttrId,0,$product['entity_id'],$defaultImage, $defaultImage));
                $db->query('INSERT INTO catalog_product_entity_varchar SET entity_type_id = ?, attribute_id = ?, store_id = ?, entity_id = ?, value = ? ON DUPLICATE KEY UPDATE value = ?', array(4,$smallImageAttrId,0,$product['entity_id'],$defaultImage, $defaultImage));
                $db->query('INSERT INTO catalog_product_entity_varchar SET entity_type_id = ?, attribute_id = ?, store_id = ?, entity_id = ?, value = ? ON DUPLICATE KEY UPDATE value = ?', array(4,$thumbnailAttrId,0,$product['entity_id'],$defaultImage, $defaultImage));
                echo 'New default image has been set for ' . $product['sku'] . PHP_EOL;
            }
        }
    }
}
