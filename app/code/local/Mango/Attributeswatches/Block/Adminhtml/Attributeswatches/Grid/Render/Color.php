<?php

class Mango_Attributeswatches_Block_Adminhtml_Attributeswatches_Grid_Render_Color extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $value = $row->getData($this->getColumn()->getIndex());
        
        
       /* $info = json_decode($value);
        //return print_r($info, false);
        if($info->{'mode'}){
            //return $info->{'mode'};
            if($info->{'mode'}==2){ /* hex color */
                return '<span style="border:solid 1px #000;background-color:#'. $value .';display:inline-block;float:left;width:30px;height:15px;"></span>&nbsp;' . $value;
          /*  }elseif($info->{'mode'}==1){ /* image */
            /*    return '<span style="background-image:url('. Mage::getBaseUrl('media') . 'attributeswatches/' . $info->{'file'} .');display:inline-block;float:left;width:30px;height:15px;"></span>&nbsp;' . $info->{'file'};
         /*   }
            
            
            
        }else{
            return false;
        }*/
        
        /*if($info["mode"]==1){
            
        }*/
        
        
        
        
        
        
        
        
        
        
    }

}

?>