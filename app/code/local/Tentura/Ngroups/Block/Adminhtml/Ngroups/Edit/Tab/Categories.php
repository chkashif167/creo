<?php

class Tentura_Ngroups_Block_Adminhtml_Ngroups_Edit_Tab_Categories extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('ngroups_form', array('legend'=>Mage::helper('ngroups')->__('Assign to Category')));
     
      $fieldset->addField('category_id', 'select', array(
          'label'     => Mage::helper('ngroups')->__('Assign Group to Category'),
          'required'  => false,
          'name'      => 'category_id',
          'values' => $this->getCategoriesArray()
      ));
      
      $fieldset->addField('categories_hide', 'select', array(
          'label'     => Mage::helper('ngroups')->__('Hide other Newsletter Group'),
          'name'      => 'categories_hide',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('ngroups')->__('Yes'),
              ),
              array(
                  'value'     => 0,
                  'label'     => Mage::helper('ngroups')->__('No'),
              ),
          ),
          'note'=> Mage::helper('ngroups')->__('Users will be able subscribe just for this Category, when user visited selected category page')
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getNgroupsData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getNgroupsData());
          Mage::getSingleton('adminhtml/session')->setNgroupsData(null);
      } elseif ( Mage::registry('ngroups_data') ) {
          $form->setValues(Mage::registry('ngroups_data')->getData());
      }
      return parent::_prepareForm();
  }
  public function getCategoriesArray() {

    $categoriesArray = Mage::getModel('catalog/category')
            ->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSort('path', 'asc')
            ->load()
            ->toArray();

    $categories[0] = array(
                'label' => "",
                'value' => ""
            );
    
    foreach ($categoriesArray as $categoryId => $category) {
        if (isset($category['name']) && isset($category['level'])) {
            
            $prefix = "";
            for ($i = 0; $i < $category['level']; $i++){
                $prefix .= "--";
            }
            
            $categories[] = array(
                'label' => $prefix.$category['name'],
                'level'  =>$category['level'],
                'value' => $categoryId
            );
        }
    }

    return $categories;
}
}