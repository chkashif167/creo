<?php
class Extensions_Sellers_Model_Options
{
  /**
   * Provide available options as a value/label array
   *
   * @return array
   */
  public function toOptionArray()
  {
    return array(
      array('value'=>30, 'label'=>'Last One Month'),
      array('value'=>60, 'label'=>'Last Two Months'),
      array('value'=>90, 'label'=>'Last Three Months'),            
      array('value'=>120, 'label'=>'Last Four Months')                     
    );
  }
}