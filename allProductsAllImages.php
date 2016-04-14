<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Products Images</title>
	<link rel="stylesheet" href="">
</head>
<body>
​

	
<table border="1" width="100%">
<tr>
<td>S.no</td>
<td>Product Id</td>
<td>Product Name</td>
<td>Product SKU</td>
<td>Product Image</td>
<td>Associated Products</td>
</tr>

<?php

require_once './app/Mage.php';
Mage::app();

set_time_limit(10000);
//ini_set("display_errors","off");



$collectionConfigurable = Mage::getResourceModel('catalog/product_collection')->addAttributeToFilter('type_id', array('eq' => 'configurable'));

$outOfStockConfis = array();
$i=1;
foreach ($collectionConfigurable as $_configurableproduct) {
    $product = Mage::getModel('catalog/product')->load($_configurableproduct->getId());
    //if (!$product->getData('is_salable')) {
      // $outOfStockConfis[] = $product->getId();
    //}
if ($product->getData('is_salable')) {
?>
<tr>
<td valign="top"><?php echo $i?></td>
<td valign="top"><?php echo $product->getId()?></td>
<td valign="top"><?php echo $product->getName()?></td>
<td valign="top"><?php echo $product->getSku()?></td>
<td valign="top"><a href="<?php echo Mage::getModel('catalog/product_media_config')
->getMediaUrl($product->getImage());?>" target="_blank" download ><?php echo Mage::getModel('catalog/product_media_config')
->getMediaUrl($product->getImage());?></a></td>
<td valign="top">
	<?php
	$associated_products = $_product->loadByAttribute('sku', $product->getSku())->getTypeInstance()->getUsedProducts();
	foreach ($associated_products as $assoc)
	{
	$assocProduct =Mage::getModel('catalog/product')->load($assoc->getId());
	 $i=0; 

	 if (count($assocProduct->getMediaGalleryImages()) > 0) {
	       foreach ($assocProduct->getMediaGalleryImages() as $_image)
	        {
	         echo $_image->url."<br>";
	         
	         }
	    }
	}
	?>
</td>
</tr>
<?php
$i++;
}
//echo $i."-->".$product->getImageUrl()."<br>".$product->getSku()."<br>";


}
?>
</table>
</body>
</html>