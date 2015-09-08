<?php
/**
 * Grid container block
 * 
 * @category    Clarion
 * @package     Clarion_FeaturedProduct
 * @author      Clarion Magento Team <magento@clariontechnologies.co.in>
 * 
 */
class Clarion_FeaturedProduct_Block_Adminhtml_Featuredproduct extends Mage_Adminhtml_Block_Widget_Grid_Container
{       
  public function __construct()
  {
    /*both these variables tell magento the location of our Grid.php(grid block) file.
     * $this->_blockGroup.'/' . $this->_controller . '_grid'
     * i.e  clarion_featuredproduct/adminhtml_featuredproduct_grid
     * $_blockGroup - is your module's name.
     * $_controller - is the path to your grid block. 
     */
    $this->_controller = 'adminhtml_featuredproduct';
    $this->_blockGroup = 'clarion_featuredproduct';
    $this->_headerText = Mage::helper('clarion_featuredproduct')->__('Manage Featured Products');
    
    parent::__construct();
    
    $this->_removeButton('add');
  }
}