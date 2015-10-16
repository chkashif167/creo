<?php
/**
 * Extensions PDF rewrite for custom attribute
 * Attribute "extensions" has to be set manually
 * Original: Sales Order Invoice Pdf default items renderer
 *
 * @category   Extensions
 * @package    Extensions_Invoice
 * @author     Jawad Nisar - Extensions <jawwad.nissar@gmail.com>
 */
 
class Extensions_Invoice_Model_Order_Pdf_Items_Invoice_Default extends Mage_Sales_Model_Order_Pdf_Items_Invoice_Default
{
    /**
     * Draw item line
	 **/
    public function draw()
    {
        $order  = $this->getOrder();
        $item   = $this->getItem();
        $pdf    = $this->getPdf();
        $page   = $this->getPage();
        $lines  = array();       

        // draw Product name
        $lines[0] = array(array(
            'text' => Mage::helper('core/string')->str_split($item->getName(), 100, true, true),
            'feed' => 110,
        ));

        // draw QTY
        $lines[0][] = array(
            'text'  => $item->getQty()*1, 
            'feed'  => 460
        );
        
        // draw Price
        $lines[0][] = array(
            'text'  => $order->formatPriceTxt($item->getPrice()),
            'feed'  => 410,
            'font'  => 'bold',
            'align' => 'right'
        );
 
        // draw Subtotal
        $lines[0][] = array(
            'text'  => $order->formatPriceTxt($item->getRowTotal()),
            'feed'  => 560,
            'font'  => 'bold',
            'align' => 'right'
        );
		
        // custom options
        $options = $this->getItemOptions();
	//echo "<pre>";
	//print_r($options);
	//echo "<pre>";
	//exit;
	$op_count = 0;  
        if ($options) {
            foreach ($options as $option) {
                // draw options label
                 if ($option['label']) {
                 $lines[][] = array(
                    'text' => Mage::helper('core/string')->str_split(strip_tags($option['label']), 50, true, true),
                    'font' => 'italic',
                    'feed' => 110
                );
                $op_count++;
               
                    $_printValue = isset($option['print_value']) ? $option['print_value'] : strip_tags($option['value']);
                    $values = explode(', ', $_printValue);
                    foreach ($values as $value) {
                        $lines[][] = array(
                            'text' => Mage::helper('core/string')->str_split($value, 50, true, true),
                            'feed' => 120
                        );
                    }
                }
            }
            if($op_count==0){            
	            $lines[][] = array(
	             'text'  => '',
	             'feed'  => 560,
	             'font'  => 'bold',
	             'align' => 'right'
	            );
	            $lines[][] = array(
	             'text'  => '',
	             'feed'  => 560,
	             'font'  => 'bold',
	             'align' => 'right'
	            );
            }else if($op_count == 1){
	            $lines[][] = array(
	             'text'  => '',
	             'feed'  => 560,
	             'font'  => 'bold',
	             'align' => 'right'
	            );
            }else if($op_count == 2){}else{}
        }else{
            $lines[][] = array(
             'text'  => '',
             'feed'  => 560,
             'font'  => 'bold',
             'align' => 'right'
            );
            $lines[][] = array(
             'text'  => '',
             'feed'  => 560,
             'font'  => 'bold',
             'align' => 'right'
            );
        }
 
        $lineBlock = array(
            'lines'  => $lines,
            'height' => 12
        );
		
        $page = $pdf->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $this->setPage($page);
 
    }
 
}