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
 * @package   Sphinx Search Ultimate
 * @version   2.3.2
 * @build     1290
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */


error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'app/Mage.php';
Mage::app();
?>

<div style="width:600px;border:1px solid #e2e2e2;margin:0 auto;padding:20px">
    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="get" style="margin: 0 auto;">
        <label for="search">Input query here: <input id="search" type="text" name="q" value="<?php echo $_GET['q'] ?>" /></label>
        <button type="submit">Search</button>
    </form>

<?php if ($query = $_GET['q']): ?>
<?php
try {
    $storeId = Mage::app()->getStore()->getId();
    $index = Mage::getResourceModel('searchindex/index_collection')
        ->addFieldToFilter('index_code', array('eq' => 'mage_catalog_product'))
        ->getFirstItem()
        ->getIndexInstance();
    $engine = Mage::helper('searchindex')->getSearchEngine();
    $start = microtime(true);
    $result = $engine->query($query, $storeId, $index);
    $end = microtime(true);
    $resultTime = round($end - $start, 4);
} catch (Exception $e) {
    ?>
    <div style="border:3px solid red;padding:20px;position:absolute;top:100px;left:10px">
        <h1 style="text-align:center;color:red">Exception:</h1>
        <pre><?php echo $e;
    die();
    ?></pre>
    </div>
<?php 
} ?>

    <h3 >Query: "<?php echo $query ?>"</h3>
    <p><b>Count:</b> <?php echo count($result) ?></p>
    <p><b>Search Speed (seconds): </b> <?php echo $resultTime ?></p>
    <h3>Results:</h3>
    <table border="1" align="center">
        <tr><th>Product ID</th></tr>
        <?php foreach ($result as $itemId => $relevance): ?>
            <tr><th><?php echo $itemId ?></th></tr>
        <?php endforeach ?>
    </table>

<?php endif ?>
</div>
