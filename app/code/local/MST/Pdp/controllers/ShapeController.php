<?php
class MST_Pdp_ShapeController extends Mage_Core_Controller_Front_Action
{
	public function getImagePagingAction(){
		$page_size = $_POST['page_size'];
		$current_page = $_POST['current_page'];
		$url = $_POST['url'];
		$category = $_POST['category'];
		$collection = Mage::getModel('pdp/shapes')->getImageCollectionByCategory($category);
		$collection_counter = Mage::getModel('pdp/shapes')->getImageCollectionByCategory($category);
		$total = count($collection_counter);
		$viewPerPage = Mage::helper('pdp')->getViewPerPage();
		$data = Mage::helper('pdp/shape')->pagingCollection($current_page, $page_size, $viewPerPage, $collection, $total, $url, $category);
		$new_data=array();
		$new_data['paging_text'] = $data['paging_text'];
		foreach ($data['collection'] as $item){
			$new_data['collection'][] = array($item->getData());
		}
		$this->getResponse()->setBody(json_encode($new_data));
	}
    public function deleteImageByIdAction()
	{
		$img_list = $_POST['img_list'];
		if ($img_list != "") {
			$imgArr = explode(',', $img_list);
			foreach ($imgArr as $img) {
				$temp = explode('_', $img);
				$id = $temp[1];
				Mage::getModel('pdp/shapes')->load($id)->delete();
			}
		}
	}
    public function loadMoreImageAction()
	{
		$current_page = $_POST['current_page'];
		$category = $_POST['category'];
		$pageSize = $_POST['page_size'];
		$pdpObject = new MST_Pdp_Block_Pdp();
		//$size = ceil($collection_counter/$page_size);
		$collection = $pdpObject->pagingShapeCollection($current_page, $category, $pageSize);
		if ( count($collection) > 0) {
			$data = array();
			$pdpObject = Mage::getModel('pdp/pdp');
			foreach ($collection as $image) {
				$data[] = $image->getData();
			}
			$this->getResponse()->setBody(json_encode($data));
		} else {
			$this->getResponse()->setBody("nomore");
		}
	}
    public function searchShapeAction()
	{
		$current_page = $_POST['current_page'];
		$category = 'all';
		$pageSize = $_POST['page_size'];
        $keyword = $_POST['keyword'];
		$pdpObject = new MST_Pdp_Block_Pdp();
		//$size = ceil($collection_counter/$page_size);
		$collection = $pdpObject->pagingShapeCollection($current_page, $category, $pageSize, $keyword);
		if ( count($collection) > 0) {
			$data = array();
			$pdpObject = Mage::getModel('pdp/pdp');
			foreach ($collection as $image) {
				$data[] = $image->getData();
			}
			$this->getResponse()->setBody(json_encode($data));
		} else {
			$this->getResponse()->setBody("nomore");
		}
	}
    public function changeShapeNameAction() {
        $request = $this->getRequest()->getPost();
        $response = array(
            'status' => 'error',
            'message' => 'Can not change shape name!'
        );
        if(isset($request['shape_id']) && $request['shape_id']) {
            if(isset($request['original_filename']) && $request['original_filename']) { 
                $shape = Mage::getModel("pdp/shapes")->load($request['shape_id']);
                $shape->setOriginalFilename($request['original_filename']);
                $shape->save();
                if($shape->getOriginalFilename() == $request['original_filename']) {
                    $response = array(
                        'status' => 'success',
                        'message' => 'Shape name successfully changed!'
                    );
                }
                
            }
        }
        $this->getResponse()->setBody(json_encode($response));
    }
    public function changeShapeTagAction() {
        $request = $this->getRequest()->getPost();
        $response = array(
            'status' => 'error',
            'message' => 'Can not change shape tag!'
        );
        if(isset($request['shape_id']) && $request['shape_id']) {
            if(isset($request['tag']) && $request['tag']) { 
                $shape = Mage::getModel("pdp/shapes")->load($request['shape_id']);
                $shape->setTag($request['tag']);
                $shape->save();
                if($shape->getTag() == $request['tag']) {
                    $response = array(
                        'status' => 'success',
                        'message' => 'Shape tag successfully changed!'
                    );
                }
                
            }
        }
        $this->getResponse()->setBody(json_encode($response));
    }
}