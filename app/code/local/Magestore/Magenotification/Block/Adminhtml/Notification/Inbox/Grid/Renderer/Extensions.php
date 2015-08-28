<?php
class Magestore_Magenotification_Block_Adminhtml_Notification_Inbox_Grid_Renderer_Extensions
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $related_extensions = $row->getRelatedExtensions();
        if(strlen($related_extensions)>100){
			return substr($related_extensions,0,100).'...';
		}else
			return $related_extensions;
    }
}
