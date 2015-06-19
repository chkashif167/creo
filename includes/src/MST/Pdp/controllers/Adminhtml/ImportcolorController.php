<?php
class MST_Pdp_Adminhtml_ImportcolorController extends Mage_Adminhtml_Controller_Action
{
    public function editAction() { 
           $this->loadLayout()

            ->_setActiveMenu('pdp/pdp');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()->createBlock('pdp/adminhtml_importcolor_edit'))

        ->_addLeft($this->getLayout()->createBlock('pdp/adminhtml_importcolor_edit_tabs'));

        

        $this->renderLayout();
    }
    function saveAction()
    {
        $resource = Mage::getSingleton('core/resource');
    	$writeConnection = $resource->getConnection('core_write');
        $readConnection = $resource->getConnection('core_write');
    	$table = $resource->getTableName('mst_pdp_colors');
    	$field="color_id,color_name,color_code,status,position";
        //get data cua cai cu
        $as = $readConnection->fetchAll('select * from '.$table);
        $oldData = array();
      foreach($as as $a)
      {
        $oldData[$a['color_code']] = $a['color_code'];
      }
       if($dataPost = $this->getRequest()->getPost())
    	{
    	  $csv_file = $_FILES['file_csv']['tmp_name'];
    	  if ( ! is_file( $csv_file ) )
       		{
       		    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('pdp')->__('File not Found'));
                 $this->_redirect('*/*/edit');
                 return;
       		}
    	  $ext = end(explode('.',$_FILES['file_csv']['name']));
          if($ext != 'csv')
          {
               Mage::getSingleton('adminhtml/session')->addError(Mage::helper('pdp')->__('File Type Not Allow'));
                 $this->_redirect('*/*/edit');
                 return;
          }
          if($dataPost['clear_data'] == '1')
          {
             $writeConnection->query('delete from '.$table);
          }
    	  $query = '';
    	  if (($handle = fopen( $csv_file, "r")) !== FALSE)
    	  {	
    			  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
    			  {
    			     //list valibeal
                     $title = $data[0];
                     $colorCode = $data[1];
                     $position = $data[2];
                     $status = ucfirst($data[3]) == 'Enabled' ? 1 : 2;
    			     if(trim($colorCode) == '')
                     continue;
                     if($dataPost['clear_data'] == '2')
                     {
                        if(array_key_exists($colorCode,$oldData))
                        {
                            $query .= "UPDATE {$table} SET color_name = '{$title}', position = '{$position}', status = '{$status}' where color_code = '{$colorCode}';";
                            continue;
                        }
                     }
    					$query .= "INSERT INTO {$table} ({$field}) VALUES (NULL,'$title','{$colorCode}','$status','{$position}');";
                    //  echo  "INSERT INTO {$table} ({$field}) VALUES (NULL,'{$data[0]}','{$data[1]}','{$data[2]}','','0','5','d');<br/>";
    			  }
    			  fclose($handle);
    	  }      
	  #Execute query
    	  $writeConnection->query($query);
          Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('pdp')->__('Import data success'));
                     $this->_redirect('*/adminhtml_color/');
                     return;
        }
    }  
}