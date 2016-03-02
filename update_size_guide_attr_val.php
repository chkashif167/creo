<?php
 require_once './app/Mage.php';
Mage::app();
Mage::setIsDeveloperMode(true);
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(0);
ini_set('memory_limit','3048M');
//ensure to set current store as product attributes are store specific
//Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
//$productCollection = Mage::getModel('catalog/product')->getCollection();
 $categoryid = $_GET['cat'];
 $domain = $_GET['dom'];
if($categoryid==''){
echo "Please provide category id";	
exit;
}
if($domain==''){
echo "Please provide domain";  
exit;
}

    $category = new Mage_Catalog_Model_Category();
    $category->load($categoryid);
    $productCollection = $category->getProductCollection();
	$i=0;
foreach($productCollection as $product) 
{

    if($domain=='beta'){
        $domain_name = 'http://beta.creoroom.com';
    }else{
        $domain_name = 'https://creoroom.com';
    }

    if($categoryid=='17'){ //Men-tshirt
        $attributeCode = "sizeguidemen";
        $att_text = '<div class="tshirtsize">
<table width="100%" border="0" cellspacing="0" cellpadding="5" style="width:100%;" class="sizeguidetable">
  <tr>
    <td align="left" colspan="4" style="border-bottom:1px solid #000;"><h3 class="heading">men size guide</h3></td>
    <td align="right" colspan="4" style="border-bottom:1px solid #000;" valign="bottom"><small>measurements are shown in inches</small></td>
  </tr>
  <tr>
    <td colspan="4"><h4>creo t-shirt MEASUREMENTS</h4></td>
    <td colspan="4"><h4>international sizing chart</h4></td>
  </tr>
  <tr>
    <th>size</th>
    <th>chest</th>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <th>size</th>
    <th>chest</th>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td bgcolor="#ebebeb">s</td>
    <td bgcolor="#ebebeb">34-36</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td bgcolor="#ebebeb">s</td>
    <td bgcolor="#ebebeb">34-36</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>m</td>
    <td>38-40</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>m</td>
    <td>38-40</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td bgcolor="#ebebeb">l</td>
    <td bgcolor="#ebebeb">42-44</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td bgcolor="#ebebeb">l</td>
    <td bgcolor="#ebebeb">42-44</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>XL</td>
    <td>46-48</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>XL</td>
    <td>46-48</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
</div>';
        echo "\n".'updating men t-shirt products'.$product->getSku()."...".$i."<br>";

    }elseif ($categoryid=='59') {// women tshirt
        $attributeCode = "sizeguidewomen";
        $att_text = '<div class="tshirtsize">
<table width="100%" border="0" cellspacing="0" cellpadding="5" style="width:100%;" class="sizeguidetable">
  <tr>
    <td align="left" colspan="4" style="border-bottom:1px solid #000;"><h3 class="heading">women size guide</h3></td>
    <td align="right" valign="bottom" colspan="4" style="border-bottom:1px solid #000;"><small>measurements are shown in inches</small></td>
  </tr>
  <tr>
    <td colspan="4"><h4>creo t-shirt MEASUREMENTS</h4></td>
    <td colspan="4"><h4>international sizing chart</h4></td>
  </tr>
  <tr>
    <th>size</th>
    <th>chest</th>
    <th>waist</th>
    <td width="50">&nbsp;</td>
    <th>size</th>
    <th>us</th>
    <th>uk</th>
    <th>italy</th>
  </tr>
  <tr>
    <td bgcolor="#ebebeb">s</td>
    <td bgcolor="#ebebeb">32</td>
    <td bgcolor="#ebebeb">24.75</td>
    <td>&nbsp;</td>
    <td bgcolor="#ebebeb">s</td>
    <td bgcolor="#ebebeb">0-2</td>
    <td bgcolor="#ebebeb">8</td>
    <td bgcolor="#ebebeb">38</td>
  </tr>
  <tr>
    <td>m</td>
    <td>34</td>
    <td>26.75</td>
    <td>&nbsp;</td>
    <td>m</td>
    <td>4-6</td>
    <td>10</td>
    <td>40</td>
  </tr>
  <tr>
    <td bgcolor="#ebebeb">l</td>
    <td bgcolor="#ebebeb">36</td>
    <td bgcolor="#ebebeb">28.75</td>
    <td>&nbsp;</td>
    <td bgcolor="#ebebeb">l</td>
    <td bgcolor="#ebebeb">8-10</td>
    <td bgcolor="#ebebeb">12</td>
    <td bgcolor="#ebebeb">42</td>
  </tr>
  <tr>
    <td>XL</td>
    <td>38</td>
    <td>30.75</td>
    <td>&nbsp;</td>
    <td>XL</td>
    <td>12-14</td>
    <td>14</td>
    <td>44</td>
  </tr>
</table>
</div>';
        echo "\n".'updating women t-shirt products'.$product->getSku()."...".$i."<br>";

}elseif ($categoryid=='18') {// men polo
        $attributeCode = "sizeguidemen";
        $att_text = '<div class="tshirtsize">
<table width="100%" border="0" cellspacing="0" cellpadding="5" style="width:100%;" class="sizeguidetable">
  <tr>
    <td align="left" colspan="4" style="border-bottom:1px solid #000;"><h3 class="heading">men\'s Polo size guide</h3></td>
    <td align="right" colspan="4" style="border-bottom:1px solid #000;" valign="bottom"><small>measurements are shown in inches</small></td>
  </tr>
  <tr>
    <td colspan="4"><h4>creo t-shirt MEASUREMENTS</h4></td>
    <td colspan="4"><h4>international sizing chart</h4></td>
  </tr>
  <tr>
    <th>size</th>
    <th>chest</th>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <th>size</th>
    <th>chest</th>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td bgcolor="#ebebeb">s</td>
    <td bgcolor="#ebebeb">32-34</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td bgcolor="#ebebeb">s</td>
    <td bgcolor="#ebebeb">34-36</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>m</td>
    <td>36-38</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>m</td>
    <td>38-40</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td bgcolor="#ebebeb">l</td>
    <td bgcolor="#ebebeb">40-42</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td bgcolor="#ebebeb">l</td>
    <td bgcolor="#ebebeb">42-44</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>XL</td>
    <td>44-46</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>XL</td>
    <td>46-48</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
</div>';
        echo "\n".'updating men polo products'.$product->getSku()."...".$i."<br>";
    }elseif ($categoryid=='60') {// women polo
        $attributeCode = "sizeguidewomen";
        $att_text = '<div class="tshirtsize">
<table width="100%" border="0" cellspacing="0" cellpadding="5" style="width:100%;" class="sizeguidetable">
  <tr>
    <td align="left" colspan="4" style="border-bottom:1px solid #000;"><h3 class="heading">women\'s Polo size guide</h3></td>
    <td align="right" valign="bottom" colspan="4" style="border-bottom:1px solid #000;"><small>measurements are shown in inches</small></td>
  </tr>
  <tr>
    <td colspan="4"><h4>creo t-shirt MEASUREMENTS</h4></td>
    <td colspan="4"><h4>international sizing chart</h4></td>
  </tr>
  <tr>
    <th>size</th>
    <th>chest</th>
    <th>waist</th>
    <td width="50">&nbsp;</td>
    <th>size</th>
    <th>us</th>
    <th>uk</th>
    <th>italy</th>
  </tr>
  <tr>
    <td bgcolor="#ebebeb">s</td>
    <td bgcolor="#ebebeb">32</td>
    <td bgcolor="#ebebeb">25</td>
    <td>&nbsp;</td>
    <td bgcolor="#ebebeb">s</td>
    <td bgcolor="#ebebeb">0</td>
    <td bgcolor="#ebebeb">6</td>
    <td bgcolor="#ebebeb">34</td>
  </tr>
  <tr>
    <td>m</td>
    <td>34</td>
    <td>27</td>
    <td>&nbsp;</td>
    <td>m</td>
    <td>2-4</td>
    <td>8</td>
    <td>36</td>
  </tr>
  <tr>
    <td bgcolor="#ebebeb">l</td>
    <td bgcolor="#ebebeb">36</td>
    <td bgcolor="#ebebeb">29</td>
    <td>&nbsp;</td>
    <td bgcolor="#ebebeb">l</td>
    <td bgcolor="#ebebeb">4-6</td>
    <td bgcolor="#ebebeb">10</td>
    <td bgcolor="#ebebeb">38</td>
  </tr>
  <tr>
    <td>XL</td>
    <td>38 1/2</td>
    <td>31 1/2</td>
    <td>&nbsp;</td>
    <td>XL</td>
    <td>8-10</td>
    <td>12</td>
    <td>40</td>
  </tr>
</table>
</div>';
        echo "\n".'updating women polo products'.$product->getSku()."...".$i."<br>";
    }

    $product = Mage::getModel('catalog/product')
                   ->load($product->getEntityId());
    $product->setData($attributeCode, $att_text)->getResource()->saveAttribute($product, $attributeCode);
$i++;
}
