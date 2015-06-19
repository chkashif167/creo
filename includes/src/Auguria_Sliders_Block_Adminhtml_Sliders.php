<?php
/**
 * @category   Auguria
 * @package    Auguria_Sliders
 * @author     Auguria
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3 (GPLv3)
 */
class Auguria_Sliders_Block_Adminhtml_Sliders extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    /**
     * Initialize banners manage page
     *
     * @return void
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_sliders';
        $this->_blockGroup = 'auguria_sliders';
        $this->_headerText = Mage::helper('auguria_sliders')->__('Manage sliders');
        $this->_addButtonLabel = Mage::helper('auguria_sliders')->__('Add Slider');
        parent::__construct();
    }
}
