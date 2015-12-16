<?php
class MST_Pdp_Adminhtml_ImporttextController extends Mage_Adminhtml_Controller_Action
{
    public function editAction() { 
           $this->loadLayout()

            ->_setActiveMenu('pdp/pdp');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()->createBlock('pdp/adminhtml_importtext_edit'))

        ->_addLeft($this->getLayout()->createBlock('pdp/adminhtml_importtext_edit_tabs'));

        

        $this->renderLayout();
    }
    function saveAction()
    {
        $resource = Mage::getSingleton('core/resource');
    	$writeConnection = $resource->getConnection('core_write');
        $readConnection = $resource->getConnection('core_write');
    	$table = $resource->getTableName('mst_pdp_texts');
    	$field="text_id,text,tags,is_popular,status,position";
        //get data cua cai cu
        $as = $readConnection->fetchAll('select * from '.$table);
        $oldData = array();
      foreach($as as $a)
      {
        $oldData[$a['text']] = $a['text'];
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
                     $text = trim($data[0]);
                     $tags = $data[1];
                     $is_popular = ucfirst($data[2]) == 'Yes' ? 1 : 2;
                     $status = ucfirst($data[4]) == 'Enabled' ? 1 : 2;
                     $position = $data[3];
    			     if($text == '')
                     continue;
                     if($dataPost['clear_data'] == '2')
                     {
                        if(array_key_exists($text,$oldData))
                        {
                            $query .= "UPDATE {$table} SET tags = '{$tags}', position = '{$position}', status = '{$status}', is_popular = '{$is_popular}' where text = '{$text}';";
                            continue;
                        }
                     }
    					$query .= "INSERT INTO {$table} ({$field}) VALUES (NULL,'{$text}','{$tags}','$is_popular','{$status}','{$position}');";
                    //  echo  "INSERT INTO {$table} ({$field}) VALUES (NULL,'{$data[0]}','{$data[1]}','{$data[2]}','','0','5','d');<br/>";
    			  }
    			  fclose($handle);
                 // echo $query; die;
    	  }      
	  #Execute query
    	  $writeConnection->query($query);
          Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('pdp')->__('Import data success'));
                     $this->_redirect('*/adminhtml_text/');
                     return;
        }
    }  
}